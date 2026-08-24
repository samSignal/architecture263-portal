<?php

use App\Http\Controllers\ArchitectDirectoryController;
use App\Http\Controllers\CouncilController;
use App\Http\Controllers\EngagementController;
use App\Http\Controllers\PlanApplicationViewController;
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
    Route::get('/architects', [ArchitectDirectoryController::class, 'index'])->name('architects.index');
    Route::get('/architects/{id}', [ArchitectDirectoryController::class, 'show'])->name('architects.show');

    Route::get('/engagements', [EngagementController::class, 'index'])->name('engagements.index');
    Route::post('/engagements', [EngagementController::class, 'store'])->name('engagements.store');
    Route::get('/engagements/{id}', [EngagementController::class, 'show'])->name('engagements.show');
    Route::post('/engagements/{id}/approve', [EngagementController::class, 'approve'])->name('engagements.approve');
    Route::post('/engagements/{id}/contract/sign', [EngagementController::class, 'signContract'])->name('engagements.contract.sign');

    Route::middleware('portal.role:architect')->group(function () {
        Route::post('/blue-book/request', [EngagementController::class, 'requestBlueBook'])->name('blue-book.request');

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

    Route::middleware('portal.role:council')->group(function () {
        Route::get('/council', [CouncilController::class, 'index'])->name('council.index');
        Route::get('/council/{id}', [CouncilController::class, 'show'])->name('council.show');
        Route::get('/council/{id}/drawings', [CouncilController::class, 'downloadDrawings'])->name('council.drawings');
        Route::get('/council/{id}/drawings/preview', [CouncilController::class, 'previewDrawings'])->name('council.drawings.preview');
        Route::get('/council/{id}/drawings/{version}', [CouncilController::class, 'downloadDrawingVersion'])->whereNumber('version')->name('council.drawings.version');
        Route::post('/council/{id}/comments', [CouncilController::class, 'storeComment'])->name('council.comments.store');
        Route::post('/council/{id}/markups', [CouncilController::class, 'storeMarkup'])->name('council.markups.store');
        Route::delete('/council/{id}/markups/{markupId}', [CouncilController::class, 'destroyMarkup'])->name('council.markups.destroy');
        Route::post('/council/{id}/decide', [CouncilController::class, 'decide'])->name('council.decide');
    });

    // Architects & clients viewing the status/review of their own plan
    // applications (submitted through the plan-approval wizard).
    Route::get('/plan-applications', [PlanApplicationViewController::class, 'index'])->name('plan-applications.index');
    Route::get('/plan-applications/{id}', [PlanApplicationViewController::class, 'show'])->name('plan-applications.show');
    Route::get('/plan-applications/{id}/drawings', [PlanApplicationViewController::class, 'downloadDrawings'])->name('plan-applications.drawings');
    Route::get('/plan-applications/{id}/drawings/preview', [PlanApplicationViewController::class, 'previewDrawings'])->name('plan-applications.drawings.preview');
    Route::get('/plan-applications/{id}/drawings/{version}', [PlanApplicationViewController::class, 'downloadDrawingVersion'])->whereNumber('version')->name('plan-applications.drawings.version');
    Route::get('/plan-applications/{id}/resubmit', [PlanApplicationViewController::class, 'resubmitForm'])->name('plan-applications.resubmit.form');
    Route::post('/plan-applications/{id}/resubmit', [PlanApplicationViewController::class, 'resubmit'])->name('plan-applications.resubmit');
});

Route::get('/', function () {
    return redirect()->route('engagements.index');
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
