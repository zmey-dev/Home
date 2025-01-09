<?php

use App\Http\Controllers\RecordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:manager'])->prefix('admin')->group(function () {
    Route::post('/user/create', [UserController::class, 'store']);

    Route::get('/records', [RecordController::class, 'index']);
    Route::delete('/records/{id}', [RecordController::class, 'admin_delete']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'role:employee'])->prefix('employee')->group(function () {
    Route::post('/records', [RecordController::class, 'store']);
    Route::post('/records/{id}', [RecordController::class, 'update']);
    Route::delete('/records/{id}', [RecordController::class, 'user_delete']);

    Route::post('/logout', [AuthController::class, 'logout']);
});