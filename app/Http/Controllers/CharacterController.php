<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Character;
use App\Models\FriendCharacter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CharacterController extends Controller
{
    // 登録ページを表示するメソッド
    public function showRegistrationForm()
    {
        return view('character.register');
    }

    // キャラクターのデータを登録するメソッド
    public function registerCharacter(Request $request)
    {
        // URLのバリデーション
        $request->validate([
            'url' => 'required|url',
            'type' => 'required|in:self,friend' // 自分のキャラクターか友達のキャラクターかを確認
        ]);

        $url = $request->input('url');
        $type = $request->input('type'); // フォームから受け取ったキャラクターのタイプ

        try {
            // URLが有効かどうか確認し、データを取得
            if (strpos($url, 'vampire-blood.net') !== false) {
                $this->fetchFromCharacterHokanjo($url, $type);
            } elseif (strpos($url, 'charaeno.com') !== false) {
                $this->fetchFromCharaeno($url, $type);
            } else {
                return redirect()->back()->with('error', '無効なURLです。');
            }

            // キャラクターが正常に登録された場合は成功メッセージを表示
            return redirect()->back()->with('status', 'キャラクターが正常に登録されました。');
        } catch (\Exception $e) {
            // エラー発生時にログにエラーを記録し、エラーメッセージを表示
            Log::error('エラー発生: ' . $e->getMessage());
            return redirect()->back()->with('error', 'データの取得に失敗しました。後ほど再試行してください。');
        }
    }

    // キャラクター保管所からデータを取得するメソッド
    private function fetchFromCharacterHokanjo($url, $type)
    {
        if (strpos($url, 'coc_pc_making.html') === false) {
            throw new \Exception('申し訳ありません。6版のみ対応しています。');
        }

        $apiUrl = str_replace('.html', '.js', $url);

        try {
            $response = Http::timeout(10)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->failed()) {
                throw new \Exception("データの取得に失敗しました。");
            }

            $characterData = $response->json();
            $this->saveCharacterData($characterData, $type);
        } catch (\Exception $e) {
            throw new \Exception('接続エラー: ' . $e->getMessage());
        }
    }

    // Charaenoからデータを取得するメソッド
    private function fetchFromCharaeno($url, $type)
    {
        if (strpos($url, '7th') !== false) {
            throw new \Exception('6版のURLが必要です。');
        }

        $id = basename($url);
        $apiUrl = "https://charaeno.com/api/v1/6th/{$id}/summary";

        try {
            $response = Http::timeout(10)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->failed()) {
                throw new \Exception("データの取得に失敗しました。");
            }

            $characterData = $response->json();
            $this->saveCharacterData($characterData, $type);
        } catch (\Exception $e) {
            throw new \Exception('接続エラー: ' . $e->getMessage());
        }
    }

    // データベースにキャラクターデータを保存するメソッド
    private function saveCharacterData($data, $type = 'self')
    {
        // 自分のキャラクターは Character モデル、友達のキャラクターは FriendCharacter モデルを使用
        if ($type === 'self') {
            $character = new Character();
        } else {
            $character = new FriendCharacter();
        }

        // 基本情報を保存
        $character->name = $data['name'] ?? '名前不明';
        $character->occupation = $data['occupation'] ?? '職業不明';
        $character->birthplace = $data['birthplace'] ?? '出身不明';
        $character->degree = $data['degree'] ?? '学位不明';
        $character->age = $this->extractNumericValue($data['age'] ?? 0, 0);
        $character->sex = $data['sex'] ?? '性別不明';

        // 能力値やスキルを保存
        $abilities = [
            'str' => $this->extractNumericValue($data['characteristics']['str'] ?? 0, 0),
            'con' => $this->extractNumericValue($data['characteristics']['con'] ?? 0, 0),
            'pow' => $this->extractNumericValue($data['characteristics']['pow'] ?? 0, 0),
            'dex' => $this->extractNumericValue($data['characteristics']['dex'] ?? 0, 0),
            'app' => $this->extractNumericValue($data['characteristics']['app'] ?? 0, 0),
            'siz' => $this->extractNumericValue($data['characteristics']['siz'] ?? 0, 0),
            'int' => $this->extractNumericValue($data['characteristics']['int'] ?? 0, 0),
            'edu' => $this->extractNumericValue($data['characteristics']['edu'] ?? 0, 0),
            'hp' => $this->extractNumericValue($data['attribute']['hp'] ?? 0, 0),
            'mp' => $this->extractNumericValue($data['attribute']['mp'] ?? 0, 0),
            'db' => $data['attribute']['db'] ?? '+0',
            'san_value' => $this->extractNumericValue($data['attribute']['san']['value'] ?? 0, 0),
            'san_max' => $this->extractNumericValue($data['attribute']['san']['max'] ?? 99, 99),
        ];

        $character->abilities = json_encode($abilities);
        $character->skills = json_encode($data['skills'] ?? []);
        $character->save();
    }

    // 数値を抽出するためのヘルパーメソッド
    private function extractNumericValue($value, $default)
    {
        if (is_numeric($value)) {
            return $value;
        } elseif (preg_match('/\d+/', $value, $matches)) {
            return $matches[0];
        }
        return $default;
    }

    // キャラクター一覧を表示するメソッド
    public function showCharacters()
    {
        $characters = Character::all();

        // abilities と skills をデコード
        foreach ($characters as $character) {
            if (is_string($character->abilities)) {
                $character->abilities = json_decode($character->abilities, true);
            }
            if (is_string($character->skills)) {
                $character->skills = json_decode($character->skills, true);
            }
        }
        return view('character.self', compact('characters'));
    }

    // 自分のキャラクターを表示するメソッド
    public function showSelfCharacters()
    {
        $characters = Character::all();

        // abilities と skills をデコード
        foreach ($characters as $character) {
            if (is_string($character->abilities)) {
                $character->abilities = json_decode($character->abilities, true);
            }
            if (is_string($character->skills)) {
                $character->skills = json_decode($character->skills, true);
            }
        }
        return view('character.self', compact('characters'));

    }

    // 編集ページを表示するメソッド
    public function editCharacter($id)
    {
        $character = Character::findOrFail($id);
        return view('character.edit', compact('character'));
    }

    // キャラクターを更新するメソッド
    public function updateCharacter(Request $request, $id)
    {
        $character = Character::findOrFail($id);

        // 基本情報の更新
        $character->name = $request->input('name');
        $character->occupation = $request->input('occupation');
        $character->age = $request->input('age');
        $character->sex = $request->input('sex');
        $character->birthplace = $request->input('birthplace');
        $character->degree = $request->input('degree');

        // 能力値の更新
        $abilities = [
            'str' => $request->input('abilities.str', 0),
            'con' => $request->input('abilities.con', 0),
            'pow' => $request->input('abilities.pow', 0),
            'dex' => $request->input('abilities.dex', 0),
            'app' => $request->input('abilities.app', 0),
            'siz' => $request->input('abilities.siz', 0),
            'int' => $request->input('abilities.int', 0),
            'edu' => $request->input('abilities.edu', 0),
            'hp' => $request->input('abilities.hp', 0),
            'mp' => $request->input('abilities.mp', 0),
            'db' => $request->input('abilities.db', '+0'),
            'san_value' => $request->input('abilities.san_value', 0),
            'san_max' => $request->input('abilities.san_max', 99),
        ];

        $character->abilities = json_encode($abilities);

        // スキルの更新
        $character->skills = json_encode($request->input('skills', []));

        $character->save();

        return redirect()->route('characters.show', ['id' => $character->id])
            ->with('status', 'キャラクターが更新されました。');
    }

    // キャラクターを削除するメソッド
    public function deleteCharacter($id)
    {
        $character = Character::findOrFail($id);
        $character->delete();

        return redirect()->route('characters.index')
            ->with('status', 'キャラクターが削除されました。');
    }

    // 自分のキャラクターのリストを表示するメソッド
    public function showSelfCharactersList()
    {
        $characters = Character::all();

        // abilities と skills をデコード
        foreach ($characters as $character) {
            if (is_string($character->abilities)) {
                $character->abilities = json_decode($character->abilities, true);
            }
            if (is_string($character->skills)) {
                $character->skills = json_decode($character->skills, true);
            }
        }
        return view('character.self', compact('characters'));
    }
}
