<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ChatbotSettingController;
use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\FaqController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::inertia('/chat', 'Chat')->name('chat');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('categories', FaqCategoryController::class)
            ->parameters(['categories' => 'category'])
            ->except('show');

        Route::resource('faqs', FaqController::class)->except('show');
        Route::resource('doctors', DoctorController::class)->except('show');

        Route::get('conversations', [ConversationController::class, 'index'])->name('conversations.index');
        Route::get('conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');

        Route::get('chatbot-settings', [ChatbotSettingController::class, 'edit'])->name('chatbot-settings.edit');
        Route::put('chatbot-settings', [ChatbotSettingController::class, 'update'])->name('chatbot-settings.update');
    });
});

require __DIR__.'/settings.php';
