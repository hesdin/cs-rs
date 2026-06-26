<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ChatController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function (): JsonResponse {
    try {
        DB::connection()->getPdo();

        return response()->json([
            'status' => 'healthy',
            'database' => 'connected',
            'timestamp' => now()->toISOString(),
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'database' => 'disconnected',
            'error' => $e->getMessage(),
        ], 503);
    }
})->name('api.health');

Route::middleware('throttle:30,1')->group(function (): void {
    Route::post('/chat', ChatController::class)->name('api.chat');
});
