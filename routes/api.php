<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ChatController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:30,1')->group(function (): void {
    Route::post('/chat', ChatController::class)->name('api.chat');
});
