<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CekController;

Route::get('/users', [UserController::class, 'getData']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::put('/users/{user}', [UserController::class, 'update']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);
Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole']);
Route::post('/users/{user}/activate', [UserController::class, 'activate']);