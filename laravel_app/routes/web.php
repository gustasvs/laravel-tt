<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;



Route::redirect('/', '/home');

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/guest', [HomeController::class, 'guest_index'])->name('guest.home');

Route::delete('/users/{user}', [HomeController::class, 'destroy'])->name('users.destroy');

Route::put('/users/{user}/update-username', [HomeController::class, 'update_username'])->name('users.update_username');

Route::put('/users/{user}', [HomeController::class, 'update_role'])->name('users.update_role');

Route::post('/images', [ImageController::class, 'store'])->name('images.store');

Route::delete('/images/{id}', [ImageController::class, 'destroy'])->name('images.destroy');

Route::post('/images/{id}/like', [ImageController::class, 'like'])->name('images.like');
Route::post('/images/{id}/dislike', [ImageController::class, 'dislike'])->name('images.dislike');

Route::put('/image/{id}', [ImageController::class, 'update_description'])->name('images.update_description');
Route::get('/image/{id}', [ImageController::class, 'show'])->name('images.show');
// Route::put('image/{id}', [ImageController::class, 'updateDescription'])->name('images.update_description');

Route::get('/users/{id}', [UserController::class, "show"])->name('users.show');
Route::get('/users/{user}/profile', [UserController::class, "profile"])->name('users.profile');
Route::post('/users/{id}/store_profile_image', [HomeController::class, 'store_profile_picture'])->name('users.store_profile_picture');
Route::post('/users/{id}/change_profile_image', [UserController::class, 'change_profile_picture'])->name('users.change_profile_picture');

// Route::resource('images', [ImageController::class]);


Auth::routes();
