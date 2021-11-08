<?php

use App\Http\Controllers\Api\MainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('todo')->group(function () {
        Route::get('/', [MainController::class, 'get']);
        Route::get('/one', [MainController::class, 'getOne']);
        Route::post('/', [MainController::class, 'store']);
        Route::put('/', [MainController::class, 'update']);
        Route::delete('/', [MainController::class, 'drop']);
    });
    Route::get('/check', [MainController::class, 'check']);
});

Route::post('/login', [MainController::class, 'login']);
Route::post('/register', [MainController::class, 'register']);
