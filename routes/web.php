<?php

use App\Http\Controllers\PlanApprovalController;
use App\Http\Controllers\PortalAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserActivationController;
use App\Http\Controllers\UserInvitationController;
use App\Http\Middleware\EnsurePortalAuthenticated;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::prefix('portal')->name('portal.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [PortalAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [PortalAuthController::class, 'login']);
    });

    Route::middleware([EnsurePortalAuthenticated::class])->group(function () {
        Route::any('/logout', [PortalAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [PortalAuthController::class, 'dashboard'])->name('dashboard');
    });
});

Route::middleware([EnsurePortalAuthenticated::class])->group(function () {
    Route::get('/plan-approval', [PlanApprovalController::class, 'index'])->name('plan-approval.index');
    Route::get('/plan-approval/step-1', [PlanApprovalController::class, 'step1'])->name('plan-approval.step1');
    Route::post('/plan-approval/step-1', [PlanApprovalController::class, 'postStep1'])->name('plan-approval.postStep1');
    Route::get('/plan-approval/step-2', [PlanApprovalController::class, 'step2'])->name('plan-approval.step2');
    Route::post('/plan-approval/step-2', [PlanApprovalController::class, 'postStep2'])->name('plan-approval.postStep2');
    Route::get('/plan-approval/step-3', [PlanApprovalController::class, 'step3'])->name('plan-approval.step3');
    Route::post('/plan-approval/step-3', [PlanApprovalController::class, 'postStep3'])->name('plan-approval.postStep3');
    Route::get('/plan-approval/step-4', [PlanApprovalController::class, 'step4'])->name('plan-approval.step4');
    Route::post('/plan-approval/submit', [PlanApprovalController::class, 'submit'])->name('plan-approval.submit');
});

Route::get('/', function () {
    return redirect()->route('plan-approval.index');
});

Route::get('/activate/{token}', [UserActivationController::class, 'show'])->name('activate.show');
Route::post('/activate/{token}', [UserActivationController::class, 'store'])->name('activate.store');

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware('auth')->name('dashboard.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users/invite', [UserInvitationController::class, 'create'])->name('users.invite.create');
    Route::post('/users/invite', [UserInvitationController::class, 'store'])->name('users.invite');
    Route::post('/users/resend/{id}', [UserInvitationController::class, 'resend'])->name('users.resend');

    Route::put('/users/{id}', [UserInvitationController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/toggle-suspend', [UserInvitationController::class, 'toggleSuspend'])->name('users.toggle-suspend');
    Route::post('/users/{id}/expire-link', [UserInvitationController::class, 'expireLink'])->name('users.expire-link');
    Route::post('/users/{id}/reactivate-link', [UserInvitationController::class, 'reactivateLink'])->name('users.reactivate-link');
});
