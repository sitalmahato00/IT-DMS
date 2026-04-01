<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Admin\StudentController;

// Public API routes (no authentication required)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Protected API routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Admin API routes
    Route::get('/admin/students/{id}', [StudentController::class, 'apiGetStudent'])
        ->middleware('role:admin');
});
