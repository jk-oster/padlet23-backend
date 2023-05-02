<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PadletController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UnsplashController;

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

//Route::get('', static function (Request $request) {
//    return response()->json(['code' => 'seas']);
//});

// Auth actions
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

// methods which need authentication - JWT Token
Route::group(['middleware' => ['api', 'auth.jwt']], static function () {
    // Auth actions
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/refresh', [AuthController::class, 'refresh']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // User actions
    Route::get('search/user/{searchTerm}', [AuthController::class, 'search']);

    // Special get Padlets
    Route::get('shared/padlet', [PadletController::class, 'getSharedPadlets']);
    Route::get('user/padlet/{id}', [PadletController::class, 'getPadletUsersByPadletId']);
    Route::get('private/padlet', [PadletController::class, 'getPrivatePadlets']);

    // Padlet actions
    Route::post('padlet/{id}/share', [PadletController::class, 'sharePadlet']);
    Route::put('padlet/{id}/toggle', [PadletController::class, 'toggle']);

    // Padlet User actions
    Route::get('padlet-user', [PadletController::class, 'getPadletUsers']);
    Route::put('padlet-user/{id}', [PadletController::class, 'acceptPadlet']);
    Route::delete('padlet-user/{id}', [PadletController::class, 'declinePadlet']);
});
// Unsplash image search
Route::get('search/image/{searchTerm}', [\App\Http\Controllers\UnsplashController::class, 'search']);

// Get metadata from url for preview component
Route::get('metadata/{url}', [\App\Http\Controllers\MetatagController::class, 'getMetaData']);

// Padlet actions
Route::get('padlet', [PadletController::class, 'index']);
Route::get('padlet/{id}', [PadletController::class, 'show']);
Route::post('padlet', [PadletController::class, 'store']);
Route::put('padlet/{id}', [PadletController::class, 'update']);
Route::get('search/padlet/{search}', [PadletController::class, 'search']);
Route::delete('padlet/{id}', [PadletController::class, 'destroy']);

// Post actions
Route::get('padlet/{id}/post', [PostController::class, 'getPostsByPadletId']);
Route::get('post/{id}', [PostController::class, 'show']);
Route::post('post', [PostController::class, 'store']);
Route::put('post/{id}', [PostController::class, 'update']);
Route::delete('post/{id}', [PostController::class, 'destroy']);
Route::get('search/padlet/{padletId}/post/{search}', [PostController::class, 'search']);

// Comment actions
Route::get('post/{id}/comment', [CommentController::class, 'getCommentsByPostId']);
Route::post('comment', [CommentController::class, 'store']);
Route::put('comment/{id}', [CommentController::class, 'update']);
Route::delete('comment/{id}', [CommentController::class, 'destroy']);
Route::get('search/post/{postId}/comment/{search}', [CommentController::class, 'search']);

// Rating actions
Route::get('post/{id}/rating', [RatingController::class, 'getRatingsByPostId']);
Route::post('rating', [RatingController::class, 'store']);
Route::put('rating/{postId}', [RatingController::class, 'update']);
Route::delete('rating/{postId}', [RatingController::class, 'destroy']);
