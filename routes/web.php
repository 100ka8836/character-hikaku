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

Route::get('/characters', [CharacterController::class, 'showCharacters'])->name('characters.index');
Route::put('/characters/{id}/update', [CharacterController::class, 'updateCharacter'])->name('characters.update');
Route::delete('/characters/{id}/delete', [CharacterController::class, 'deleteCharacter'])->name('characters.delete');
Route::get('/characters/{id}/edit', [CharacterController::class, 'editCharacter'])->name('characters.edit');
Route::get('/register', [CharacterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [CharacterController::class, 'registerCharacter'])->name('register.character');