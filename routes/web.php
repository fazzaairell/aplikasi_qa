<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TestRunController;
use App\Http\Controllers\BugController;

// Endpoint untuk Test Run & Eksekusi Hasil Tes
Route::post('/test-runs', [TestRunController::class, 'store']);
Route::get('/test-runs/{id}', [TestRunController::class, 'show']);
Route::post('/test-results/{id}/execute', [TestRunController::class, 'updateResult']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/bugs', [BugController::class, 'index']);
Route::put('/bugs/{id}/status', [BugController::class, 'updateStatus']);

// Halaman login

Route::get('/test', function () {
    return view('test');
});
Route::get('/login', function () {
    return view('auth.login'); // Tanpa 'auth.' karena sudah di luar
})->name('login');


