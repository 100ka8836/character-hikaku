<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\FriendCharacterController;

/*
|-------------------------------------------------------------------------- 
| Web Routes 
|-------------------------------------------------------------------------- 
|
| Here is where you can register web routes for your application. These 
| routes are loaded by the RouteServiceProvider within a group which 
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 自分のキャラクター一覧ページ
Route::get('/self-characters', [CharacterController::class, 'showSelfCharacters'])->name('characters.self');


// 友達のキャラクター一覧ページ
Route::get('/friend-characters', [FriendCharacterController::class, 'index'])->name('friend_characters.index');

// 陣ごとページ
Route::get('/characters/jin', [CharacterController::class, 'showJinPage'])->name('characters.jin');

// キャラクター関連の操作
Route::get('/characters', [CharacterController::class, 'showCharacters'])->name('characters.index');
Route::get('/characters/{id}/edit', [CharacterController::class, 'editCharacter'])->name('characters.edit');
Route::put('/characters/{id}', [CharacterController::class, 'update'])->name('characters.update');
Route::delete('/characters/{id}', [CharacterController::class, 'destroyCharacter'])->name('characters.destroy');
Route::post('/characters/{id}/copy', [CharacterController::class, 'copy'])->name('characters.copy');

// キャラクター登録
Route::get('/register', [CharacterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [CharacterController::class, 'registerCharacter'])->name('register.character');

// 友達キャラクターの編集ページ
Route::get('/friend-characters/{id}/edit', [FriendCharacterController::class, 'edit'])->name('friend_characters.edit');

// 友達キャラクターの削除
Route::delete('/friend-characters/{id}', [FriendCharacterController::class, 'destroy'])->name('friend_characters.destroy');

// 友達キャラクターのコピー
Route::post('/friend-characters/{id}/copy', [FriendCharacterController::class, 'copy'])->name('friend_characters.copy');
