<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/generate', [AIController::class, 'generate'])->name('generate')->middleware('auth:sanctum');

Route::group(["middleware" => ["auth:sanctum"]],function(){
    Route::post('/logout', [AuthController::class, 'logout']);
});