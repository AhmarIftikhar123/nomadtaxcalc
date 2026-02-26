<?php

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\LandingPage\LandingPageController;
use App\Http\Controllers\Newsletter\NewsletterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\TaxCalculator\SavedCalculationController;
use App\Http\Controllers\TaxCalculator\TaxCalculatorController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/auth.php';
// {-------------- < 20 requests per minute > -----------------}
Route::middleware('throttle:20,1')->group(function () {
    // {-------------- < Public Routes > -----------------}
    Route::get('/', [LandingPageController::class, 'index'])->name('home');

    // Tax Calculator Routes (public — no auth required)
    Route::prefix('tax-calculator')->group(function () {
        Route::get('/', [TaxCalculatorController::class, 'index'])->name('tax-calculator.index');
        Route::post('/step-1', [TaxCalculatorController::class, 'storeStep1'])->name('tax-calculator.step-1');
        Route::post('/step-2', [TaxCalculatorController::class, 'storeStep2'])->name('tax-calculator.step-2.store');
        // Public shared results page (token acts as access credential)
        Route::get('/shared/{token}', [TaxCalculatorController::class, 'viewShared'])->name('tax-calculator.shared');
    });

    // Currency endpoint — public, lazy-loaded by frontend when territorial country added
    Route::get('/currencies', [CurrencyController::class, 'index'])->name('currencies.index');

    // Newsletter Routes
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');


    // Tax Calculator — Auth-Only Routes
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::post('/tax-calculator/save', [TaxCalculatorController::class, 'saveCalculation'])
            ->name('tax-calculator.save');
        Route::post('/tax-calculator/email-results', [TaxCalculatorController::class, 'sendEmail'])
            ->name('tax-calculator.email-results');
        Route::post('/tax-calculator/generate-link', [TaxCalculatorController::class, 'generateLink'])
            ->name('tax-calculator.generate-link');

        // My Calculations
        Route::resource('my-calculations', SavedCalculationController::class)
            ->only(['index', 'destroy']);

        // {-------------- < User Profile Routes > -----------------}
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        });
    });
    // temporary Route
    Route::get('dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');
});
