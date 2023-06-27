<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('/csrf-token', function () {
//     return response()->json(['csrf_token' => csrf_token()])
//         ->header('Access-Control-Allow-Credentials', 'true');
// });

Route::get('/csrf-token', [HomeController::class, 'redirect_home']);
Route::get('/redirect_home', [HomeController::class, 'redirect_home']);

Route::get('/users', [UserController::class, 'get_users']);
Route::post('/users/{id}/store_profile_image', [HomeController::class, 'api_store_profile_picture'])->middleware('auth:sanctum');
Route::post('/users/{id}/change_profile_image', [UserController::class, 'api_change_profile_picture'])->middleware('auth:sanctum');
Route::post('/users/{id}/change_name', [UserController::class, 'change_name'])->middleware('auth:sanctum');
Route::post('/users/{id}/delete', [UserController::class, 'api_delete'])->middleware('auth:sanctum');
Route::get('/user/{id}', [UserController::class, 'get_user']);

Route::put('/users/{id}', [HomeController::class, 'api_update_role'])->middleware('auth:sanctum');

Route::get('/image/{id}', [ImageController::class, 'get_image']);
Route::get('/images', [ImageController::class, 'get_images']);
Route::post('/images', [ImageController::class, 'api_store'])->middleware('auth:sanctum');
Route::delete('/images/{id}', [ImageController::class, 'api_destroy'])->middleware('auth:sanctum');
Route::post('/images/{id}/like', [ImageController::class, 'api_like'])->middleware('auth:sanctum');
Route::post('/images/{id}/view', [ImageController::class, 'api_view'])->middleware('auth:sanctum');
Route::post('/images/{id}/dislike', [ImageController::class, 'api_dislike'])->middleware('auth:sanctum');
Route::put('/images/{id}/update_desc', [ImageController::class, 'api_update_description'])->middleware('auth:sanctum');

Route::get('/get_auth_user', [UserController::class, 'get_auth_user'])->middleware('auth:sanctum');
Route::get('/get_user_token', [UserController::class, 'get_user_token'])->middleware('auth:sanctum');

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);


Route::post('/log', [HomeController::class, 'save_log'])->middleware('auth:sanctum');
Route::get('/logs', [HomeController::class, 'get_logs'])->middleware('auth:sanctum');
Route::delete('/logs', [HomeController::class, 'delete_logs'])->middleware('auth:sanctum');

Route::get('/get_user_images', [UserController::class, 'get_user_images'])->middleware('auth:sanctum');
Route::get('/get_some_user_images/{id}', [UserController::class, 'get_some_user_images'])->middleware('auth:sanctum');







// Auth::routes();
