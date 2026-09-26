<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/generate', [AIController::class, 'generate'])->name('generate')->middleware('auth:sanctum');

Route::group(["middleware" => ["auth:sanctum"]],function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::get('/conversations/{id}', [ConversationController::class, 'get']);
    Route::delete('/conversations/{id}', [ConversationController::class, 'destroy']);
    Route::post('/password/change', [AuthController::class, 'changePassword']);
});

Route::prefix('password')->group(function () {
        Route::post('/send-code', [PasswordResetController::class, 'sendCode']);
        Route::post('/verify-code', [PasswordResetController::class, 'verifyCode']);
        Route::post('/reset', [PasswordResetController::class, 'reset']);
    });