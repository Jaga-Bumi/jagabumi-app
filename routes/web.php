<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\QuestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home.index');
})->name('home');

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('auth/web3', [AuthController::class, 'web3Login'])->name('auth.web3');

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', function () {
        return view('pages.profile.index');
    })->name('profile');
});

Route::prefix('quests')->name('quests.')->group(function(){
    Route::get('', [QuestController::class, 'readAll'])->name('all');
    Route::get('{id}', [QuestController::class, 'readOne'])->name('one');
    Route::post('create', [QuestController::class, 'create'])->name('create');
    Route::put('update/{id}', [QuestController::class, 'update'])->name('update');
    Route::delete('destroy/{id}', [QuestController::class, 'destroy'])->name('destroy');
});

// Route::get('/articles', function () {
//     return view('pages.articles.index');
// })->name('articles.all');
Route::prefix('articles')->name('articles.')->group(function(){
    Route::get('', [ArticleController::class, 'readAll'])->name('all');
    Route::get('{id}', [ArticleController::class, 'readOne'])->name('one');
//     Route::post('create', [ArticleController::class, 'create'])->name('create');
//     Route::put('update/{id}', [ArticleController::class, 'update'])->name('update');
//     Route::delete('destroy/{id}', [ArticleController::class, 'destroy'])->name('destroy');
});

// Route::get('/articles/{id}', function ($id) {
//     return view('pages.articles.show', compact('id'));
// })->name('articles.one');

Route::get('/leaderboard', function () {
    return view('pages.leaderboard.index');
})->name('leaderboard');

Route::get('dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

Route::prefix('organizations')->name('org.')->group(function(){
    Route::get('', [OrganizationController::class, 'readAll'])->name('all');
    Route::get('{id}', [OrganizationController::class, 'readOne'])->name('one');
//     Route::put('update/{id}', [OrganizationController::class, 'update'])->name('update');
//     Route::delete('destroy/{id}', [OrganizationController::class, 'destroy'])->name('destroy');
});

// Route::prefix('prizes')->name('prizes.')->group(function(){
//     Route::get('', [PrizeController::class, 'readAll'])->name('all');
//     Route::get('{id}', [PrizeController::class, 'readOne'])->name('one');
//     Route::post('create', [PrizeController::class, 'create'])->name('create');
//     Route::put('update/{id}', [PrizeController::class, 'update'])->name('update');
//     Route::delete('destroy/{id}', [PrizeController::class, 'destroy'])->name('destroy');
// });

// Route::prefix('users')->name('users.')->group(function(){
//     Route::get('', [UserController::class, 'readAll'])->name('all');
//     Route::get('{id}', [UserController::class, 'readOne'])->name('one');
//     Route::put('update/{id}', [UserController::class, 'update'])->name('update');
//     Route::delete('destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
// });

