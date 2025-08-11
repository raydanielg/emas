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
use App\Http\Controllers\HeadmasterController;
use App\Http\Controllers\HeadmasterPagesController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminPagesController;

Route::get('/', function () { return redirect('/login'); });

// Admin Panel
Route::middleware(['web'])->prefix('admin')->name('admin.')->group(function () {
    // Auth
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected admin pages
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [AdminPagesController::class, 'dashboard'])->name('dashboard');
    });
});

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

    // Headmaster Panel (role: headmaster)
    Route::middleware(['web','auth'])->prefix('headmaster')->name('headmaster.')->group(function () {
        Route::get('/', [HeadmasterController::class, 'index'])->name('dashboard');
        Route::get('/upload', [HeadmasterController::class, 'uploadForm'])->name('upload');
        Route::post('/upload', [HeadmasterController::class, 'uploadStore'])->name('upload.store');

        // Students
        Route::get('/students/register', [HeadmasterPagesController::class, 'studentsRegister'])->name('students.register');
        Route::get('/students/manage', [HeadmasterPagesController::class, 'studentsManage'])->name('students.manage');
        Route::get('/students', [HeadmasterPagesController::class, 'studentsIndex'])->name('students.index');

        // Teachers
        Route::get('/teachers/proposals', [HeadmasterPagesController::class, 'teachersProposals'])->name('teachers.proposals');
        Route::get('/teachers/selected', [HeadmasterPagesController::class, 'teachersSelected'])->name('teachers.selected');

        // Reports
        Route::get('/reports', [HeadmasterPagesController::class, 'reportsIndex'])->name('reports.index');
        Route::get('/reports/results', [HeadmasterPagesController::class, 'reportsResults'])->name('reports.results');
        // Reports: Requests (create + rollback)
        Route::get('/reports/requests/create', [\App\Http\Controllers\HeadmasterRequestsController::class, 'create'])->name('reports.requests.create');
        Route::post('/reports/requests', [\App\Http\Controllers\HeadmasterRequestsController::class, 'store'])->name('reports.requests.store');
        Route::get('/reports/requests/{id}', [\App\Http\Controllers\HeadmasterRequestsController::class, 'show'])->name('reports.requests.show');
        Route::post('/reports/requests/{id}/cancel', [\App\Http\Controllers\HeadmasterRequestsController::class, 'cancel'])->name('reports.requests.cancel');
        Route::get('/reports/requests/rollback', [\App\Http\Controllers\HeadmasterRequestsController::class, 'rollbackCreate'])->name('reports.requests.rollback.create');
        Route::post('/reports/requests/rollback', [\App\Http\Controllers\HeadmasterRequestsController::class, 'rollbackStore'])->name('reports.requests.rollback.store');

        // Institution
        Route::get('/institution/profile', [HeadmasterPagesController::class, 'institutionProfile'])->name('institution.profile');
        Route::get('/institution/manage', [HeadmasterPagesController::class, 'institutionManage'])->name('institution.manage');
        Route::get('/institution/performance', [HeadmasterPagesController::class, 'institutionPerformance'])->name('institution.performance');

        // Settings
        Route::get('/settings', [HeadmasterPagesController::class, 'settingsIndex'])->name('settings.index');
        Route::post('/settings', [HeadmasterPagesController::class, 'settingsSave'])->name('settings.save');

        // Headmaster Profile (separate from general user settings)
        Route::get('/profile', [HeadmasterPagesController::class, 'profileShow'])->name('profile');
        Route::post('/profile', [HeadmasterPagesController::class, 'profileUpdate'])->name('profile.update');
        Route::post('/profile/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('password.update');
        Route::post('/profile/suggestion', [HeadmasterPagesController::class, 'suggestionStore'])->name('suggestion.store');

        // My Requests
        Route::get('/requests/pending', [\App\Http\Controllers\HeadmasterRequestsController::class, 'pending'])->name('requests.pending');
        Route::get('/requests/approved', [\App\Http\Controllers\HeadmasterRequestsController::class, 'approved'])->name('requests.approved');
        Route::get('/requests/need-approval', [\App\Http\Controllers\HeadmasterRequestsController::class, 'needApproval'])->name('requests.need_approval');

        // Students
        Route::get('/students', [\App\Http\Controllers\HeadmasterStudentsController::class, 'index'])->name('students.index');
        Route::get('/students/register', [\App\Http\Controllers\HeadmasterStudentsController::class, 'register'])->name('students.register');
        Route::post('/students', [\App\Http\Controllers\HeadmasterStudentsController::class, 'storeManual'])->name('students.store');
        Route::get('/students/template/{form}', [\App\Http\Controllers\HeadmasterStudentsController::class, 'downloadTemplate'])->name('students.template');
        Route::get('/students/template-excel/{form}', [\App\Http\Controllers\HeadmasterStudentsController::class, 'downloadTemplateExcel'])->name('students.template_excel');
        Route::post('/students/bulk-upload', [\App\Http\Controllers\HeadmasterStudentsController::class, 'bulkUpload'])->name('students.bulk_upload');
        Route::patch('/students/{id}/subjects', [\App\Http\Controllers\HeadmasterStudentsController::class, 'updateSubjects'])->name('students.update_subjects');
        Route::post('/students/{id}/photo', [\App\Http\Controllers\HeadmasterStudentsController::class, 'uploadImage'])->name('students.upload_image');
        Route::get('/students/assign-subjects', [\App\Http\Controllers\HeadmasterStudentsController::class, 'assignSubjects'])->name('students.assign');
        Route::get('/students/{id}', [\App\Http\Controllers\HeadmasterStudentsController::class, 'showProfile'])->name('students.show');
        Route::delete('/students/{id}', [\App\Http\Controllers\HeadmasterStudentsController::class, 'destroy'])->name('students.destroy');

        // Subjects management
        Route::get('/subjects', [\App\Http\Controllers\HeadmasterSubjectsController::class, 'index'])->name('subjects.index');
        Route::post('/subjects', [\App\Http\Controllers\HeadmasterSubjectsController::class, 'store'])->name('subjects.store');
        Route::get('/subjects/{id}/edit', [\App\Http\Controllers\HeadmasterSubjectsController::class, 'edit'])->name('subjects.edit');
        Route::put('/subjects/{id}', [\App\Http\Controllers\HeadmasterSubjectsController::class, 'update'])->name('subjects.update');
        Route::get('/subjects/{id}', [\App\Http\Controllers\HeadmasterSubjectsController::class, 'show'])->name('subjects.show');
        Route::delete('/subjects/{id}', [\App\Http\Controllers\HeadmasterSubjectsController::class, 'destroy'])->name('subjects.destroy');
        Route::patch('/subjects/{id}/assign-teacher', [\App\Http\Controllers\HeadmasterSubjectsController::class, 'assignTeacher'])->name('subjects.assign_teacher');

        // Teachers management
        Route::get('/teachers', [\App\Http\Controllers\HeadmasterTeachersController::class, 'index'])->name('teachers.index');
        Route::post('/teachers', [\App\Http\Controllers\HeadmasterTeachersController::class, 'store'])->name('teachers.store');
        Route::patch('/teachers/{id}/assign-subject', [\App\Http\Controllers\HeadmasterTeachersController::class, 'assignSubject'])->name('teachers.assign_subject');

        // Teacher proposals
        Route::get('/teachers/proposals', [\App\Http\Controllers\HeadmasterTeachersController::class, 'proposalsIndex'])->name('teachers.proposals');
        Route::post('/teachers/proposals', [\App\Http\Controllers\HeadmasterTeachersController::class, 'proposalsStore'])->name('teachers.proposals.store');
        Route::get('/teachers/proposals/{id}', [\App\Http\Controllers\HeadmasterTeachersController::class, 'proposalsShow'])->name('teachers.proposals.show');

        // Selected for Marking
        Route::get('/teachers/selected', [\App\Http\Controllers\HeadmasterTeachersController::class, 'selectedIndex'])->name('teachers.selected');
        Route::get('/teachers/selected/{id}', [\App\Http\Controllers\HeadmasterTeachersController::class, 'selectedShow'])->name('teachers.selected.show');
        Route::post('/teachers/selected/{id}/generate-letter', [\App\Http\Controllers\HeadmasterTeachersController::class, 'selectedGenerateLetter'])->name('teachers.selected.generate_letter');
        Route::get('/teachers/selected/{id}/letter', [\App\Http\Controllers\HeadmasterTeachersController::class, 'selectedViewLetter'])->name('teachers.selected.letter');
    });
});
