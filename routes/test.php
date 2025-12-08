<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationRequestController;
use App\Http\Controllers\PrizeController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\QuestParticipantController;
use Illuminate\Support\Facades\Route;

// Test routes

Route::get('/', [HomeController::class, 'ewfjewfjb'])->name('home');

Route::get('/quests', [QuestController::class, 'getAll'])->name('quests.all');

Route::get('/organizations', [OrganizationController::class, 'getAll'])->name('organizations.all');

Route::get('/articles', [ArticleController::class, 'getAll'])->name('articles.all');

Route::get('/join-us', [OrganizationRequestController::class, 'index'])->name('join-us');

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

Route::post('auth/web3', [AuthController::class, 'web3Login'])->name('auth.web3');

Route::middleware('auth')->group(function () {
  Route::post('logout', [AuthController::class, 'logout'])->name('logout');
  Route::post('/join-us/submit', [OrganizationRequestController::class, 'store'])->name('join-us.store');
});


// aaron routes
Route::get('/my-participations/{id}', [QuestParticipantController::class, 'myParticipations']);
Route::get('/my-prizes/{id}', [PrizeController::class, 'myPrizes']);
Route::get('/quests/{id}', [QuestController::class, 'readOne']);
Route::get('/quests/{id}/submissions', [QuestParticipantController::class, 'submissions']);
