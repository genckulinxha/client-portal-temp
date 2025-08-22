<?php

use App\Http\Controllers\ClientAuth\ClientLoginController;
use App\Http\Controllers\ClientAuth\ClientGoogleController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\TaskController;
use App\Http\Controllers\Client\DocumentController;
use App\Http\Controllers\Client\CalendarController;
use Illuminate\Support\Facades\Route;

// Client Authentication Routes
Route::prefix('client')->name('client.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('client.guest')->group(function () {
        Route::get('/login', [ClientLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [ClientLoginController::class, 'login']);
        Route::get('/auth/google', [ClientGoogleController::class, 'redirect'])->name('auth.google');
        Route::get('/auth/google/callback', [ClientGoogleController::class, 'callback'])->name('auth.google.callback');
    });

    // Authenticated client routes
    Route::middleware('client.auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [ClientLoginController::class, 'logout'])->name('logout');

        // Task management
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

        // Document management
        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::post('/documents/upload', [DocumentController::class, 'upload'])->name('documents.upload');
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

        // Calendar
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

        // Chat
        Route::get('/chat', [\App\Http\Controllers\Client\ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{conversation}', [\App\Http\Controllers\Client\ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{conversation}/messages', [\App\Http\Controllers\Client\ChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/{conversation}/messages', [\App\Http\Controllers\Client\ChatController::class, 'getMessages'])->name('chat.messages');
    });
});