<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', function () {
    return view('pages.home.index');
})->name('home');

// Quests
Route::get('/quests', function () {
    return view('pages.quests.index');
})->name('quests.index');

Route::get('/quests/{id}', function ($id) {
    return view('pages.quests.show', compact('id'));
})->name('quests.show');

// Articles
Route::get('/articles', function () {
    return view('pages.articles.index');
})->name('articles.index');

Route::get('/articles/{id}', function ($id) {
    return view('pages.articles.show', compact('id'));
})->name('articles.show');

// Leaderboard
Route::get('/leaderboard', function () {
    return view('pages.leaderboard.index');
})->name('leaderboard');

// Dashboard (legacy - redirect to home)
Route::get('dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

// Public routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('auth/web3', [AuthController::class, 'web3Login'])->name('auth.web3');

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile
    Route::get('/profile', function () {
        return view('pages.profile.index');
    })->name('profile');
});

