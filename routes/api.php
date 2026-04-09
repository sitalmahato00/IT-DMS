<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\LandingController;

// Public API routes (no authentication required)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/landing', [LandingController::class, 'data'])->name('api.landing');
Route::post('/convert-date', function (Request $request) {
    try {
        $date = $request->input('date');
        $direction = $request->input('direction', 'ad-to-bs');
        
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        $result = $direction === 'bs-to-ad'
            ? \App\Helpers\NepaliContentHelper::convertBsToAd($date)
            : \App\Helpers\NepaliContentHelper::convertAdToBs($date);

        return response()->json(['result' => $result]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Conversion failed: ' . $e->getMessage()], 400);
    }
})->middleware('throttle:60,1');

// Protected API routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Admin API routes
    Route::get('/admin/students/{id}', [StudentController::class, 'apiGetStudent'])
        ->middleware('role:admin');
});
