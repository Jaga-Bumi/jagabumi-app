<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('auth/web3', [AuthController::class, 'web3Login'])->name('auth.web3');
});

Route::middleware('auth')->group(function () {
    Route::get('dashboard', function () {
        return view('dashboard', [
            'user' => auth()->user(),
        ]);
    })->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
