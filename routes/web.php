<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TestRunController;
use App\Http\Controllers\BugController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\TestSuiteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Middleware\PreventBackHistory;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman awal (root) — arahkan sesuai status login & role
Route::get('/', function () {
    if (Auth::check()) {
        $role = strtolower(Auth::user()->role);

        return match(true) {
            $role === 'admin' => redirect()->route('dashboard'),
            in_array($role, ['qa lead', 'qa tester']) => redirect()->route('dashboard.qa'),
            $role === 'developer' => redirect()->route('dashboard.developer'),
            default => redirect()->route('projects.index'),
        };
    }
    return redirect()->route('login');
});

// Halaman Tamu & Autentikasi
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Reset Password (bisa diakses tanpa login)
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Halaman Test (Opsional)
Route::get('/test', function () {
    return view('test');
});

// Rute yang Diproteksi Middleware Auth & PreventBackHistory
Route::middleware(['auth', PreventBackHistory::class])->group(function () {

    // Tombol Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ==========================================
    // Profil Saya (modal — foto, nama, email, password)
    // ==========================================
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // ==========================================
    // Dashboard QA & Developer — proteksi per role
    // ==========================================
    Route::get('/dashboard/qa', [DashboardController::class, 'qa'])
        ->name('dashboard.qa')
        ->middleware('role:qa lead,qa tester');

    Route::get('/dashboard/developer', [DashboardController::class, 'developer'])
        ->name('dashboard.developer')
        ->middleware('role:developer');

    // ==========================================
    // Kelompok Khusus ADMIN (Dashboard & Users)
    // ==========================================
    Route::middleware(['admin'])->group(function () {
        // Dashboard Admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // 6. Manajemen Pengguna
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ==========================================
    // Kelompok Umum (Bisa diakses Admin & User biasa)
    // ==========================================

    // 1. Manajemen Proyek
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // 2. Manajemen Requirements (RTM)
    Route::resource('requirements', RequirementController::class);

    // 3. Test Suites & Test Cases
    Route::get('/test-suites', [TestSuiteController::class, 'index'])->name('test-suites.index');
    Route::post('/test-suites', [TestSuiteController::class, 'storeSuite'])->name('test-suites.store');
    Route::delete('/test-suites/{id}', [TestSuiteController::class, 'destroySuite'])->name('test-suites.destroy');
    Route::post('/test-cases', [TestSuiteController::class, 'storeCase'])->name('test-cases.store');
    Route::delete('/test-cases/{id}', [TestSuiteController::class, 'destroyCase'])->name('test-cases.destroy');

    // 4. Test Runs & Eksekusi Hasil Tes
    Route::get('/test-runs', [TestRunController::class, 'index'])->name('test-runs.index');
    Route::post('/test-runs', [TestRunController::class, 'store'])->name('test-runs.store');
    Route::get('/test-runs/{id}', [TestRunController::class, 'show'])->name('test-runs.show');
    Route::put('/test-runs/{id}', [TestRunController::class, 'update'])->name('test-runs.update');
    Route::delete('/test-runs/{id}', [TestRunController::class, 'destroy'])->name('test-runs.destroy');
    Route::post('/test-results/{id}/execute', [TestRunController::class, 'updateResult'])->name('test-results.execute');
    Route::patch('/test-results/{testResultId}/update', [TestRunController::class, 'updateResult'])->name('test-results.update');

    // 5. Bug Tracker
    Route::get('/bugs', [BugController::class, 'index'])->name('bugs.index');
    Route::patch('/bugs/{id}/status', [BugController::class, 'updateStatus'])->name('bugs.update-status');

});