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
            // エラーログを記録し、ユーザーに一般的なメッセージを表示
            Log::error('エラー発生: ' . $e->getMessage());
            return redirect()->back()->with('error', 'データの取得に失敗しました。後ほど再試行してください。');
        }
    }

    // キャラクター保管所からデータを取得
    private function fetchFromCharacterHokanjo($url)
    {
        // 6版のURLを確認
        if (strpos($url, 'coc_pc_making.html') === false) {
            throw new \Exception('申し訳ありません。6版のみ対応しています。');
        }

        // APIエンドポイントの作成（HTMLをJSに変換）
        $apiUrl = str_replace('.html', '.js', $url);

        try {
            $response = Http::timeout(5)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->failed()) {
                $status = $response->status();
                $body = $response->body();
                throw new \Exception("データの取得に失敗しました。ステータスコード: $status, 内容: $body");
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
        // 7版のURLが含まれている場合はエラーを返す
        if (strpos($url, '7th') !== false) {
            throw new \Exception('6版のURLが必要です。');
        }

        // Charaenoの6版エンドポイントを使用
        $id = basename($url);
        $apiUrl = "https://charaeno.com/api/v1/6th/{$id}/summary";

        try {
            $response = Http::timeout(5)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->failed()) {
                $status = $response->status();
                $body = $response->body();
                throw new \Exception("データの取得に失敗しました。ステータスコード: $status, 内容: $body");
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

        // 年齢を数値として扱い、非数値の場合はデフォルト値を設定
        if (isset($data['age']) && preg_match('/\d+/', $data['age'], $matches)) {
            $character->age = $matches[0];  // 最初に見つかった数値を使用
        } else {
            $character->age = 0;  // デフォルト値
        }

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

        // 能力値をJSON形式で保存
        $character->abilities = json_encode($abilities);

        // スキル情報が空の場合に対応
        $skills = $data['skills'] ?? [];
        if (!is_array($skills)) {
            $skills = []; // スキル情報が空なら空配列として扱う
        }
        $character->skills = json_encode($skills);

        // データベースに保存
        $character->save();
    }


    private function extractNumericValue($value, $default)
    {
        // 数値が含まれていればその数値を返し、含まれていなければデフォルト値を返す
        if (is_numeric($value)) {
            return $value;
        } elseif (preg_match('/\d+/', $value, $matches)) {
            return $matches[0];  // 最初に見つかった数値を返す
        }
        return $default;  // デフォルト値
    }

    // キャラクター一覧を表示するメソッド
    public function showCharacters()
    {
        // データベースから全てのキャラクターを取得
        $characters = Character::all();

        // 各キャラクターのabilitiesとskillsをデコード
        foreach ($characters as $character) {
            $character->abilities = json_decode($character->abilities, true);

            // スキルが空の場合は空配列を設定
            $skills = json_decode($character->skills, true);
            if (!is_array($skills)) {
                $character->skills = [];
            } else {
                // スキルが正しく連想配列であることを確認
                $character->skills = array_filter($skills, function ($skill) {
                    return isset($skill['name']) && isset($skill['value']);
                });
            }
        }

        // 一覧ページを表示し、キャラクターを渡す
        return view('character.index', compact('characters'));
    }


    // 編集ページを表示するメソッド
    public function editCharacter($id)
    {
        // キャラクターをIDで取得
        $character = Character::findOrFail($id);

        // キャラクターのabilitiesとskillsをデコードして編集ページに渡す
        $character->abilities = json_decode($character->abilities, true);
        $character->skills = json_decode($character->skills, true);

        // スキルが空の場合は空配列を設定
        if (!is_array($character->skills)) {
            $character->skills = [];
        }

        // 編集ビューを表示し、キャラクター情報を渡す
        return view('character.edit', compact('character'));
    }

    // 編集メソッド
    public function updateCharacter(Request $request, $id)
    {
        // バリデーションを追加
        $request->validate([
            'name' => 'required|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'age' => 'nullable|numeric|min:0',
            'sex' => 'nullable|string|max:10',
            'birthplace' => 'nullable|string|max:255',
            'degree' => 'nullable|string|max:255',
            'abilities.str' => 'nullable|numeric|min:0',
            'abilities.con' => 'nullable|numeric|min:0',
            'abilities.pow' => 'nullable|numeric|min:0',
            'abilities.dex' => 'nullable|numeric|min:0',
            'abilities.app' => 'nullable|numeric|min:0',
            'abilities.siz' => 'nullable|numeric|min:0',
            'abilities.int' => 'nullable|numeric|min:0',
            'abilities.edu' => 'nullable|numeric|min:0',
            'abilities.hp' => 'nullable|numeric|min:0',
            'abilities.mp' => 'nullable|numeric|min:0',
            'abilities.db' => 'nullable|string|max:10',
            'abilities.san_value' => 'nullable|numeric|min:0',
            'abilities.san_max' => 'nullable|numeric|min:0',
        ]);

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

        // JSON形式で能力値を保存
        $character->abilities = json_encode($abilities);

        // スキルの更新
        $skills = $request->input('skills', []);
        if (!empty($skills) && is_array($skills)) {
            // スキルデータが連想配列であることを確認
            $validSkills = array_filter($skills, function ($skill) {
                return isset($skill['name']) && isset($skill['value']);
            });
            $character->skills = json_encode($validSkills);
        } else {
            $character->skills = json_encode([]); // スキルが空の場合は空配列にする
        }

        // キャラクターの保存
        $character->save();

        return redirect()->route('characters.index')->with('status', 'キャラクターが更新されました。');
    }

    //削除メソッド
    public function deleteCharacter($id)
    {
        $character = Character::findOrFail($id);
        $character->delete(); // キャラクターを削除
        return redirect()->route('characters.index')->with('status', 'キャラクターが削除されました。');
    }
}
