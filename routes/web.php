<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));
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

// Public routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('auth/web3', [AuthController::class, 'web3Login'])->name('auth.web3');

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});


Route::get('home', function(){
    return 'home';
});

Route::prefix('quests')->name('quests.')->group(function(){
    Route::get('', [QuestController::class, 'getAll'])->name('all');
    Route::get('{id}', [QuestController::class, 'getOne'])->name('one');
    Route::post('create', [QuestController::class, 'create'])->name('create');
    Route::put('update/{id}', [QuestController::class, 'update'])->name('update');
    Route::delete('destroy/{id}', [QuestController::class, 'destroy'])->name('destroy');
});







