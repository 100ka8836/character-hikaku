<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Character;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CharacterController extends Controller
{
    // 登録ページを表示する
    public function showRegistrationForm()
    {
        return view('character.register');
    }

    // データを登録する
    public function registerCharacter(Request $request)
    {
        // URLバリデーション
        $request->validate([
            'url' => 'required|url'
        ]);

        $url = $request->input('url');

        try {
            // URLが有効かどうか確認し、データを取得
            if (strpos($url, 'vampire-blood.net') !== false) {
                $this->fetchFromCharacterHokanjo($url);
            } elseif (strpos($url, 'charaeno.com') !== false) {
                $this->fetchFromCharaeno($url);
            } else {
                return redirect()->back()->with('error', '無効なURLです。');
            }

            // キャラクターが正常に登録された場合のみ成功メッセージを表示
            return redirect()->back()->with('status', 'キャラクターが正常に登録されました。');
        } catch (\Exception $e) {
            Log::error('エラー発生: ' . $e->getMessage());
            return redirect()->back()->with('error', 'データの取得に失敗しました。後ほど再試行してください。');
        }
    }

    // キャラクター保管所からデータを取得
    private function fetchFromCharacterHokanjo($url)
    {
        if (strpos($url, 'coc_pc_making.html') === false) {
            throw new \Exception('申し訳ありません。6版のみ対応しています。');
        }

        $apiUrl = str_replace('.html', '.js', $url);

        try {
            $response = Http::timeout(5)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->failed()) {
                throw new \Exception("データの取得に失敗しました。");
            }

            $characterData = $response->json();
            $this->saveCharacterData($characterData);
        } catch (\Exception $e) {
            throw new \Exception('接続エラー: ' . $e->getMessage());
        }
    }

    // Charaenoからデータを取得
    private function fetchFromCharaeno($url)
    {
        if (strpos($url, '7th') !== false) {
            throw new \Exception('6版のURLが必要です。');
        }

        $id = basename($url);
        $apiUrl = "https://charaeno.com/api/v1/6th/{$id}/summary";

        try {
            $response = Http::timeout(5)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->failed()) {
                throw new \Exception("データの取得に失敗しました。");
            }

            $characterData = $response->json();
            $this->saveCharacterData($characterData);
        } catch (\Exception $e) {
            throw new \Exception('接続エラー: ' . $e->getMessage());
        }
    }

    // データベースにキャラクターデータを保存
    private function saveCharacterData($data)
    {
        $character = new Character();

        // 基本情報を保存
        $character->name = $data['name'] ?? '名前不明';
        $character->occupation = $data['occupation'] ?? '職業不明';
        $character->birthplace = $data['birthplace'] ?? '出身不明';
        $character->degree = $data['degree'] ?? '学位不明';
        $character->age = $this->extractNumericValue($data['age'] ?? 0, 0);
        $character->sex = $data['sex'] ?? '性別不明';

        // 能力値をまとめて保存
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

        $skills = $data['skills'] ?? [];
        $character->skills = json_encode($skills);

        $character->save();
    }

    private function extractNumericValue($value, $default)
    {
        if (is_numeric($value)) {
            return $value;
        } elseif (preg_match('/\d+/', $value, $matches)) {
            return $matches[0];
        }
        return $default;
    }

    // キャラクター一覧を表示
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

        return view('character.index', compact('characters'));
    }


    // 編集ページを表示するメソッド
    public function editCharacter($id)
    {
        $character = Character::findOrFail($id);
        return view('character.edit', compact('character'));
    }

    // キャラクター更新メソッド
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
        $skills = $request->input('skills', []);
        $character->skills = json_encode($skills);

        $character->save();

        return redirect()->route('characters.index')->with('status', 'キャラクターが更新されました。');
    }

    // 削除機能
    public function destroy($id)
    {
        $character = Character::findOrFail($id);
        $character->delete();

        return redirect()->route('characters.index')->with('success', 'キャラクターが削除されました');
    }

    // キャラクターをコピーする
    public function copy($id)
    {
        $character = Character::findOrFail($id);
        $newCharacter = $character->replicate();
        $newCharacter->name = $newCharacter->name . ' - コピー';
        $newCharacter->save();

        return redirect()->route('characters.index')->with('success', 'キャラクターがコピーされました');
    }

    // 自分のキャラクターを表示
    public function showSelfCharacters()
    {
        $characters = Character::where('type', 'self')->get();

        // abilities と skills をデコード
        foreach ($characters as $character) {
            if (is_string($character->abilities)) {
                $character->abilities = json_decode($character->abilities, true);
            }
            if (is_string($character->skills)) {
                $character->skills = json_decode($character->skills, true);
            }
        }

        return view('character.index', compact('characters'));
    }

    // 友達のキャラクターを表示
    public function showFriendCharacters()
    {
        $characters = Character::where('type', 'friend')->get();

        // abilities と skills をデコード
        foreach ($characters as $character) {
            if (is_string($character->abilities)) {
                $character->abilities = json_decode($character->abilities, true);
            }
            if (is_string($character->skills)) {
                $character->skills = json_decode($character->skills, true);
            }
        }

        return view('character.index', compact('characters'));
    }

    // 陣ごとページを表示
    public function showJinPage()
    {
        $selfCharacters = Character::where('type', 'self')->get();
        $friendCharacters = Character::where('type', 'friend')->get();

        // abilities と skills をデコード
        foreach ($selfCharacters as $character) {
            if (is_string($character->abilities)) {
                $character->abilities = json_decode($character->abilities, true);
            }
            if (is_string($character->skills)) {
                $character->skills = json_decode($character->skills, true);
            }
        }

        foreach ($friendCharacters as $character) {
            if (is_string($character->abilities)) {
                $character->abilities = json_decode($character->abilities, true);
            }
            if (is_string($character->skills)) {
                $character->skills = json_decode($character->skills, true);
            }
        }

        return view('character.jin', compact('selfCharacters', 'friendCharacters'));
    }
}
