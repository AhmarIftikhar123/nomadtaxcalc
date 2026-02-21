<?php

use App\Http\Controllers\LandingPage\LandingPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\TaxCalculator\TaxCalculatorController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/auth.php';

// {-------------- < Custom Routes > -----------------}
Route::get('/', [LandingPageController::class, 'index'])->name('home');

// Tax Calculator Routes
Route::prefix('tax-calculator')->group(function () {
    Route::get('/', [TaxCalculatorController::class, 'index'])->name('tax-calculator.index');
    Route::post('/step-1', [TaxCalculatorController::class, 'storeStep1'])->name('tax-calculator.step-1');
    Route::post('/step-2', [TaxCalculatorController::class, 'storeStep2'])->name('tax-calculator.step-2.store');
});
// {-------------- </ Custom Routes > -----------------}

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
