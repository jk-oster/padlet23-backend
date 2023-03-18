<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
});
