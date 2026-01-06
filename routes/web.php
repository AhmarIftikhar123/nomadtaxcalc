<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/auth.php';

// {-------------- < Custom Routes > -----------------}
// Route::inertia('students', 'Students/Index', ['first' => 'Student A', 'second' => 'Student B']);
Route::get('students/{name?}/{father?}', function ($name = 'Guest', $father = 'User') {
    return Inertia::render('Students/Index', ['name' => $name, 'father' => $father]);
});
Route::fallback(fn() => Inertia::render('Errors/NotFound'));
// {-------------- </ Custom Routes > -----------------}

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
