<?php

use App\Http\Controllers\Application\AcceptApplicationController;
use App\Http\Controllers\Application\ApplicationController;
use App\Http\Controllers\Application\ApplyToCampaignController;
use App\Http\Controllers\Application\RejectApplicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordUpdateController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RefreshTokenController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SendVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Brand\BudgetController;
use App\Http\Controllers\Brand\EmergencyStopController;
use App\Http\Controllers\Campaign\CampaignController;
use App\Http\Controllers\Campaign\CloseCampaignController;
use App\Http\Controllers\Campaign\CompleteCampaignController;
use App\Http\Controllers\Campaign\LastMinuteCampaignsController;
use App\Http\Controllers\Campaign\PauseCampaignController;
use App\Http\Controllers\Campaign\PublishCampaignController;
use App\Http\Controllers\Creator\CalendarController;
use App\Http\Controllers\Creator\IncomeSummaryController;
use App\Http\Controllers\Creator\PortfolioController;
use App\Http\Controllers\Creator\PriceListController;
use App\Http\Controllers\Draft\ApproveDraftController;
use App\Http\Controllers\Draft\DraftController;
use App\Http\Controllers\Draft\RequestDraftChangesController;
use App\Http\Controllers\Draft\SubmitDraftController;
use App\Http\Controllers\Outcome\OutcomeController;
use App\Http\Controllers\Payment\InvoiceController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Reporting\ReportingController;
use App\Http\Controllers\Reputation\RatingController;
use App\Http\Controllers\Sales\SalesAttributionController;
use App\Http\Controllers\Sales\SalesDashboardController;
use App\Http\Controllers\Social\FeedController;
use App\Http\Controllers\Social\FollowController;
use App\Http\Controllers\Stripe\OnboardController;
use App\Http\Controllers\VideoPitch\VideoPitchController;
use Illuminate\Support\Facades\Route;

Route::post('register', RegisterController::class)->name('register');
Route::post('login', LoginController::class)->middleware('throttle:5,1')->name('login');
Route::get('email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware('signed')
    ->name('verification.verify');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', LogoutController::class)->name('logout');
    Route::post('token/refresh', RefreshTokenController::class)->name('token.refresh');

    Route::get('me', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('user', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('verified')->group(function () {
        Route::delete('user', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::patch('user/password', PasswordUpdateController::class)->name('password.update');
    });

    Route::post('email/verification-notification', SendVerificationNotificationController::class)
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Static campaign routes must come BEFORE the apiResource so they don't collide with /campaigns/{campaign}
    Route::get('campaigns/last-minute', LastMinuteCampaignsController::class)->name('campaigns.last-minute');

    Route::apiResource('campaigns', CampaignController::class);
    Route::post('campaigns/{campaign}/publish', PublishCampaignController::class)->name('campaigns.publish');
    Route::post('campaigns/{campaign}/pause', PauseCampaignController::class)->name('campaigns.pause');
    Route::post('campaigns/{campaign}/close', CloseCampaignController::class)->name('campaigns.close');
    Route::post('campaigns/{campaign}/complete', CompleteCampaignController::class)->name('campaigns.complete');

    Route::post('campaigns/{campaign}/apply', ApplyToCampaignController::class)->name('campaigns.apply');
    Route::get('campaigns/{campaign}/applications', [ApplicationController::class, 'indexForCampaign'])->name('campaigns.applications.index');

    Route::get('applications/mine', [ApplicationController::class, 'mine'])->name('applications.mine');
    Route::get('applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::post('applications/{application}/accept', AcceptApplicationController::class)->name('applications.accept');
    Route::post('applications/{application}/reject', RejectApplicationController::class)->name('applications.reject');

    Route::post('applications/{application}/drafts', SubmitDraftController::class)->name('drafts.submit');
    Route::get('applications/{application}/drafts', [DraftController::class, 'indexForApplication'])->name('drafts.index');
    Route::get('drafts/{draft}', [DraftController::class, 'show'])->name('drafts.show');
    Route::post('drafts/{draft}/approve', ApproveDraftController::class)->name('drafts.approve');
    Route::post('drafts/{draft}/request-changes', RequestDraftChangesController::class)->name('drafts.request-changes');

    Route::get('campaigns/{campaign}/outcomes', [OutcomeController::class, 'indexForCampaign'])->name('outcomes.index');
    Route::get('outcomes/{outcome}', [OutcomeController::class, 'show'])->name('outcomes.show');
    Route::post('outcomes/{outcome}/record', [OutcomeController::class, 'record'])->name('outcomes.record');

    Route::post('brand/emergency-stop', EmergencyStopController::class)->name('brand.emergency-stop');

    // Portfolio
    Route::get('me/portfolio', [PortfolioController::class, 'showMine'])->name('portfolio.mine');
    Route::patch('me/portfolio', [PortfolioController::class, 'update'])->name('portfolio.update');
    Route::get('users/{user}/portfolio', [PortfolioController::class, 'showForUser'])->name('portfolio.show');

    // Price list
    Route::get('me/price-list', [PriceListController::class, 'showMine'])->name('price-list.mine');
    Route::get('users/{user}/price-list', [PriceListController::class, 'showForUser'])->name('price-list.show');
    Route::post('me/price-list/items', [PriceListController::class, 'setItem'])->name('price-list.items.set');
    Route::delete('me/price-list/items/{item}', [PriceListController::class, 'deleteItem'])->name('price-list.items.delete');
    Route::post('me/price-list/packages', [PriceListController::class, 'createPackage'])->name('price-list.packages.create');
    Route::delete('me/price-list/packages/{package}', [PriceListController::class, 'deletePackage'])->name('price-list.packages.delete');

    // Ratings
    Route::post('applications/{application}/rating', [RatingController::class, 'rateApplication'])->name('applications.rate');
    Route::get('users/{influencer}/ratings', [RatingController::class, 'indexForInfluencer'])->name('users.ratings');

    // Calendar + Income
    Route::get('me/calendar', CalendarController::class)->name('calendar.mine');
    Route::get('me/income-summary', IncomeSummaryController::class)->name('income.summary');

    // Stripe onboarding
    Route::post('me/stripe/onboard', [OnboardController::class, 'start'])->name('stripe.onboard.start');
    Route::post('me/stripe/onboard/complete', [OnboardController::class, 'complete'])->name('stripe.onboard.complete');

    // Payments + Invoices
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

    // Brand budget
    Route::get('me/budget', [BudgetController::class, 'show'])->name('budget.show');
    Route::patch('me/budget', [BudgetController::class, 'update'])->name('budget.update');

    // Sales attribution (admin)
    Route::get('me/sales-attribution', [SalesAttributionController::class, 'show'])->name('sales.attribution');
    Route::patch('admin/brands/{brand}/sales-rep', [SalesAttributionController::class, 'assignSalesRep'])->name('sales.assign');
    Route::get('me/sales-dashboard', SalesDashboardController::class)->name('sales.dashboard');

    // Reporting
    Route::get('reports/aggregate', [ReportingController::class, 'aggregate'])->name('reports.aggregate');
    Route::get('reports/export', [ReportingController::class, 'export'])->name('reports.export');

    // Video pitches
    Route::post('pitches', [VideoPitchController::class, 'send'])->name('pitches.send');
    Route::get('pitches/mine', [VideoPitchController::class, 'mine'])->name('pitches.mine');
    Route::get('pitches/{pitch}', [VideoPitchController::class, 'show'])->name('pitches.show');
    Route::post('pitches/{pitch}/accept', [VideoPitchController::class, 'accept'])->name('pitches.accept');
    Route::post('pitches/{pitch}/reject', [VideoPitchController::class, 'reject'])->name('pitches.reject');

    // Social: follow + feed
    Route::post('users/{user}/follow', [FollowController::class, 'follow'])->name('users.follow');
    Route::delete('users/{user}/follow', [FollowController::class, 'unfollow'])->name('users.unfollow');
    Route::get('me/following', [FollowController::class, 'following'])->name('users.following');
    Route::get('me/followers', [FollowController::class, 'followers'])->name('users.followers');
    Route::get('me/feed', FeedController::class)->name('feed');
});
