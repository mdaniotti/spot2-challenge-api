<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShortUrlController;

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});


// Short URL API routes
Route::prefix('urls')->group(function () {
    Route::get('/', [ShortUrlController::class, 'index']);
    Route::post('/', [ShortUrlController::class, 'store']);
    Route::get('/{id}', [ShortUrlController::class, 'show']);
    Route::put('/{id}', [ShortUrlController::class, 'update']);
    Route::delete('/{id}', [ShortUrlController::class, 'destroy']);
});

// Redirect route for short codes (sin prefijo api)
Route::get('/{code}', [ShortUrlController::class, 'redirect']);
