<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Marking\StudentsController;
use App\Http\Controllers\Marking\CentresController;
use App\Http\Controllers\Marking\MarksController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FiltersController;
use App\Http\Controllers\NotificationsController;

Route::get('/', function () { return redirect('/login'); });

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/password/forgot', function () { return view('auth.forgot'); })->name('password.request');

// Protected routes
Route::middleware('auth')->group(function(){
    Route::get('/dashboard', function () { return view('user.dashboard'); })->name('dashboard');
    Route::get('/api/dashboard/summary', [DashboardController::class, 'summary'])->name('api.dashboard.summary');

    // Settings
    Route::get('/settings/profile', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function(){
        Route::get('/registration', [ReportsController::class, 'registration'])->name('registration');
        Route::get('/progress', [ReportsController::class, 'progress'])->name('progress');
        // Live API for Vue app
        Route::get('/api/progress', [ReportsController::class, 'progressApi'])->name('api.progress');
    });

    // Notifications
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/latest', [NotificationsController::class, 'latest'])->name('notifications.latest');
    Route::get('/notifications/{id}', [NotificationsController::class, 'show'])->name('notifications.show');

    // Support (simple chat)
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support/send', [SupportController::class, 'send'])->name('support.send');

    // Global filters API
    Route::prefix('api/filters')->name('api.filters.')->group(function(){
        Route::get('/regions', [FiltersController::class, 'regions'])->name('regions');
        Route::get('/districts', [FiltersController::class, 'districts'])->name('districts');
        Route::get('/schools', [FiltersController::class, 'schools'])->name('schools');
        Route::get('/forms', [FiltersController::class, 'forms'])->name('forms');
        Route::post('/save', [FiltersController::class, 'saveSelection'])->name('save');
    });

    // Marking UI
    Route::prefix('marking')->name('marking.')->group(function () {
        Route::get('/', function(){ return view('marking.index'); })->name('index');
        // Students
        Route::get('/students', [StudentsController::class, 'index'])->name('students');
        Route::get('/students/{id}', [StudentsController::class, 'show'])->name('students.show');
        // Centres
        Route::get('/centres', [CentresController::class, 'index'])->name('centres');
        Route::get('/centres/{school}/sheet', [CentresController::class, 'sheet'])->name('centres.sheet');
        // Marks API
        Route::post('/api/marks', [MarksController::class, 'upsert'])->name('api.marks.upsert');
        Route::get('/api/assignments', [MarksController::class, 'assignments'])->name('api.assignments');
        Route::get('/api/recent', [MarksController::class, 'recent'])->name('api.recent');
        Route::get('/ca', function(){ return view('marking.ca'); })->name('ca');
    });
});
