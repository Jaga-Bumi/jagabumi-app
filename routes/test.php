<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationMemberController;
use App\Http\Controllers\OrganizationRequestController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\QuestParticipantController;
use Illuminate\Support\Facades\Route;

// Test routes

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/quests', [QuestController::class, 'getAll'])->name('quests.all');
Route::get('/quests/{slug}', [QuestController::class, 'getDetail'])->name('quests.detail');

Route::get('/organizations', [OrganizationController::class, 'getAll'])->name('organizations.all');
Route::get('/organizations/{slug}', [OrganizationController::class, 'show'])->name('organizations.show');

Route::get('/articles', [ArticleController::class, 'getAll'])->name('articles.all');

Route::get('/join-us', [OrganizationRequestController::class, 'index'])->name('join-us');

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

Route::post('auth/web3', [AuthController::class, 'web3Login'])->name('auth.web3');

Route::middleware('auth')->group(function () {
  Route::post('logout', [AuthController::class, 'logout'])->name('logout');
  
  Route::get('/profile', [HomeController::class, 'profile'])->name('profile.index');
  Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard.index');
  
  Route::post('/join-us/submit', [OrganizationRequestController::class, 'store'])->name('join-us.store');
  
  Route::post('/quests/{questId}/join', [QuestParticipantController::class, 'join'])->name('quests.join');
  Route::delete('/quests/{questId}/cancel', [QuestParticipantController::class, 'cancelParticipation'])->name('quests.cancel');
  Route::post('/quests/{questId}/submit-proof', [QuestParticipantController::class, 'submitProof'])->name('quests.submit-proof');
  
  // Attendance routes
  Route::post('/quests/{questId}/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('quests.attendance.check-in');
  Route::post('/quests/{questId}/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('quests.attendance.check-out');
  
  // Organization dashboard routes
  Route::prefix('organization')->group(function () {
    Route::get('/dashboard', [OrganizationController::class, 'dashboard'])->name('organization.dashboard');
    Route::get('/profile', [OrganizationController::class, 'profile'])->name('organization.profile');
    Route::post('/switch/{orgId}', [OrganizationController::class, 'switchOrganization'])->name('organization.switch');
    Route::get('/create', [OrganizationController::class, 'createView'])->name('organization.create');
    Route::post('/store', [OrganizationController::class, 'create'])->name('organization.store');
    Route::post('/{id}/update', [OrganizationController::class, 'update'])->name('organization.update');
    
    // Quest management
    Route::get('/quests', [QuestController::class, 'organizationQuests'])->name('organization.quests.index');
    Route::get('/quests/create', [QuestController::class, 'createView'])->name('organization.quests.create');
    Route::get('/quests/{id}', [QuestController::class, 'showQuest'])->name('organization.quests.show');
    Route::post('/quests/store', [QuestController::class, 'create'])->name('organization.quests.store');
    Route::put('/quests/{id}', [QuestController::class, 'update'])->name('organization.quests.update');
    Route::delete('/quests/{id}', [QuestController::class, 'destroy'])->name('organization.quests.destroy');
    
    // Member management
    Route::get('/members', [OrganizationMemberController::class, 'index'])->name('organization.members.index');
    Route::post('/members/{orgId}/invite', [OrganizationMemberController::class, 'invite'])->name('organization.members.invite');
    Route::delete('/members/{memberId}', [OrganizationMemberController::class, 'removeMember'])->name('organization.members.remove');
    Route::post('/members/{memberId}/resend', [OrganizationMemberController::class, 'resendInvitation'])->name('organization.members.resend');
    
    // Submission management
    Route::get('/submissions', [QuestParticipantController::class, 'organizationSubmissions'])->name('organization.submissions');
    Route::post('/submissions/{submissionId}/approve', [QuestParticipantController::class, 'approveSubmission'])->name('organization.submissions.approve');
    Route::post('/submissions/{submissionId}/reject', [QuestParticipantController::class, 'rejectSubmission'])->name('organization.submissions.reject');
    
    // Prize distribution
    Route::get('/prizes', [QuestController::class, 'organizationPrizes'])->name('organization.prizes');
    Route::post('/prizes/distribute', [QuestController::class, 'distributeRewards'])->name('organization.prizes.distribute');
    
  });
  
  // Admin routes
  Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/organization-requests', [AdminController::class, 'organizationRequestsView'])->name('admin.organization-requests');
    Route::post('/organization-requests/{id}/approve', [AdminController::class, 'approveOrganizationRequest'])->name('admin.organization-requests.approve');
    Route::post('/organization-requests/{id}/reject', [AdminController::class, 'rejectOrganizationRequest'])->name('admin.organization-requests.reject');
    
    Route::get('/quests', [AdminController::class, 'questsView'])->name('admin.quests');
    Route::post('/quests/{id}/approve', [AdminController::class, 'approveQuest'])->name('admin.quests.approve');
    Route::post('/quests/{id}/reject', [AdminController::class, 'rejectQuest'])->name('admin.quests.reject');
  });
});