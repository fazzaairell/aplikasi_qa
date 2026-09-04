<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BugController;
use App\Http\Controllers\TestRunController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Autentikasi Mobile (Login untuk mendapatkan Sanctum Token)
// Menggunakan method login API (bukan return redirect web)
Route::post('/login', [LoginController::class, 'apiLogin']); 

// 2. Rute yang wajib menyertakan Bearer Token (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // Mendapatkan data user yang sedang login di aplikasi mobile
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    });

    // Logout mobile (menghapus token aktif)
    Route::post('/logout', [LoginController::class, 'apiLogout']);

    // ==========================================
    // Fitur Proyek untuk Mobile
    // ==========================================
    Route::get('/projects', [ProjectController::class, 'apiIndex']);
    Route::get('/projects/{project}', [ProjectController::class, 'apiShow']);

    // ==========================================
    // Fitur Bug Tracker untuk Mobile
    // ==========================================
    Route::get('/bugs', [BugController::class, 'apiIndex']);
    Route::post('/bugs', [BugController::class, 'apiStore']);
    Route::get('/bugs/{id}', [BugController::class, 'apiShow']);
    Route::patch('/bugs/{id}/status', [BugController::class, 'apiUpdateStatus']);

    // ==========================================
    // Fitur Test Runs / Eksekusi Tes (Opsional)
    // ==========================================
    Route::get('/test-runs', [TestRunController::class, 'apiIndex']);
    Route::post('/test-results/{id}/execute', [TestRunController::class, 'apiUpdateResult']);
});