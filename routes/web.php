<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

// Public routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('auth/web3', [AuthController::class, 'web3Login'])->name('auth.web3');
Route::get('api/web3-config', [AuthController::class, 'getWeb3Config'])->name('web3.config');

Route::get('dashboard', function () {
    return view('pages.dashboard.index', [
        'user' => Auth::user() ?? (object)[
            'name' => 'Guest',
            'email' => '-',
            'verifier_id' => '-',
            'wallet_address' => '-'
        ],
    ]);
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
