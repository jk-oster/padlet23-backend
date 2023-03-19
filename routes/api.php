<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PadletController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('', static function (Request $request) {
    return response()->json(['code' => 'seas']);
});

Route::post('auth/login', [AuthController::class, 'login']);

// methods which need authentication - JWT Token
Route::group(['middleware' => ['api', 'auth.jwt', 'auth.admin']], static function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/refresh', [AuthController::class, 'refresh']);
    Route::get('auth/me', [AuthController::class, 'me']);
//    Route::get('auth/register', [AuthController::class, 'register']);
    Route::get('shared/padlet', [PadletController::class, 'getSharedPadlets']);
    Route::get('private/padlet', [PadletController::class, 'getPrivatePadlets']);
});

Route::get('padlet', [PadletController::class, 'index']);
Route::get('padlet/{id}', [PadletController::class, 'show']);
Route::post('padlet', [PadletController::class, 'store']);
Route::put('padlet/{id}', [PadletController::class, 'update']);
Route::delete('padlet/{id}', [PadletController::class, 'destroy']);
Route::post('padlet/{id}/share', [PadletController::class, 'sharePadlet']);
Route::get('search/padlet/{search}', [PadletController::class, 'search']);

Route::get('padlet/{id}/post', [PostController::class, 'getPostsByPadletId']);
Route::get('post/{id}', [PostController::class, 'show']);
Route::post('post', [PostController::class, 'store']);
Route::put('post/{id}', [PostController::class, 'update']);
Route::delete('post/{id}', [PostController::class, 'destroy']);
Route::get('search/padlet/{padletId}/post/{search}', [PostController::class, 'search']);

