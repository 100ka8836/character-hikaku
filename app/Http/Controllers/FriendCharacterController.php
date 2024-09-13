<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FriendCharacter;

class FriendCharacterController extends Controller
{
    public function index()
    {
        $characters = FriendCharacter::all(); // FriendCharacter モデルを使ってすべてのフレンドキャラクターを取得
        return view('character.Friend', compact('characters'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            // 他のフィールドのバリデーションルール
        ]);

        FriendCharacter::create($validatedData);

        return redirect()->route('friend_characters.index');
    }

    public function edit($id)
    {
        $friendCharacter = FriendCharacter::findOrFail($id);
        return view('character.editFriend', compact('friendCharacter')); // 編集フォームのビュー
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            // 他のフィールドのバリデーションルール
        ]);

        $friendCharacter = FriendCharacter::findOrFail($id);
        $friendCharacter->update($validatedData);

        return redirect()->route('friend_characters.index');
    }

    public function destroy($id)
    {
        $friendCharacter = FriendCharacter::findOrFail($id);
        $friendCharacter->delete();

        return redirect()->route('friend_characters.index');
    }

    public function show($id)
    {
        $friendCharacter = FriendCharacter::findOrFail($id);
        return view('character.showFriend', compact('friendCharacter'));
    }

    public function copy($id)
    {
        $character = FriendCharacter::findOrFail($id);
        $newCharacter = $character->replicate();
        $newCharacter->save();

        return redirect()->route('friend_characters.index');
    }
}
