<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CharacterController;

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

// 自分のキャラクター
Route::get('/characters/self', [CharacterController::class, 'showSelfCharacters'])->name('characters.self');

// 友達のキャラクター
Route::get('/characters/friend', [CharacterController::class, 'showFriendCharacters'])->name('characters.friends'); // ルート名を統一

// 陣ごとページ
Route::get('/characters/jin', [CharacterController::class, 'showJinPage'])->name('characters.jin');

// キャラクター関連の操作
Route::get('/characters', [CharacterController::class, 'showCharacters'])->name('characters.index');
Route::get('/characters/{id}/edit', [CharacterController::class, 'editCharacter'])->name('characters.edit');
Route::put('/characters/{id}/update', [CharacterController::class, 'updateCharacter'])->name('characters.update');
Route::delete('/characters/{id}', [CharacterController::class, 'destroy'])->name('characters.destroy');
Route::post('/characters/{id}/copy', [CharacterController::class, 'copy'])->name('characters.copy');

// キャラクター登録
Route::get('/register', [CharacterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [CharacterController::class, 'registerCharacter'])->name('register.character');
