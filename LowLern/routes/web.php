<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CleaningController;

// Redirect หน้าแรกไป Login
Route::get('/', function () {
    return redirect()->route('login');
});

// ทุก Route ต้อง Login ก่อนถึงจะเข้าได้
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 📊 Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // 📅 Calendar
    Route::get('/calendar', function () {
        return Inertia::render('Calendar/Index');
    })->name('calendar');

    // 🧹 House Cleaning
    Route::get('/cleaning', function () {
        return Inertia::render('Cleaning/Index');
    })->name('cleaning');

    // 📚 Academic Tracker
    Route::get('/academic', function () {
        return Inertia::render('Academic/Index');
    })->name('academic');

    // 🧹 House Cleaning Routes
    Route::get('/cleaning', [CleaningController::class, 'index'])->name('cleaning');
    Route::post('/cleaning/areas', [CleaningController::class, 'storeArea'])->name('cleaning.areas.store');
    Route::post('/cleaning/tasks', [CleaningController::class, 'storeTask'])->name('cleaning.tasks.store');
    Route::post('/cleaning/tasks/{log}', [CleaningController::class, 'updateStatus'])->name('cleaning.tasks.update');

});

require __DIR__.'/auth.php';