<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Health check endpoint for Docker
Route::get('/up', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'app' => config('app.name'),
    ]);
});

Route::get('/', function () {
    // Check if user is already authenticated
    if (auth()->check()) {
        return redirect('/admin');
    }
    // Check if client is already authenticated
    if (session('client_id')) {
        return redirect()->route('client.dashboard');
    }
    // Default to client login for new visitors
    return redirect()->route('client.login');
});

Route::get('/login', function () {
    // Redirect generic /login to client login by default
    return redirect()->route('client.login');
});

Route::get('/dashboard', function () {
    return redirect('/admin');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/client.php';
