<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagsController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TwoFactorAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [ApiAuthController::class, 'login'])->name('login');
Route::post('/verifyOtp', [TwoFactorAuthController::class, 'verifyOtp']);

Route::group(["middleware" => ['auth:sanctum', 'twoFactorCode'] ], function () {
    Route::prefix('tags')->group(function (){
        Route::get('/', [TagsController::class, 'index']);
        Route::post('/', [TagsController::class, 'store']);
        Route::patch('/{id}', [TagsController::class, 'update']);
        Route::delete('/{id}', [TagsController::class, 'delete']);
    });
    Route::prefix('posts')->group(function (){
        Route::get('/', [PostController::class, 'show']);
        Route::get('/show/{id}', [PostController::class, 'showById']);
        Route::post('/', [PostController::class, 'store']);
        Route::post('/{id}', [PostController::class, 'update']);
        Route::post('/delete/{id}', [PostController::class, 'softDelete']);
        Route::post('/restore/{id}', [PostController::class, 'restore']);
        Route::get('/deleted', [PostController::class, 'showDeleted']);
    });

    Route::get('/stats', [AdminController::class, 'stats']);
});
