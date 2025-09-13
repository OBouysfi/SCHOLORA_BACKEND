<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\NewsleterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Tutor\AboutStepController;
use App\Http\Controllers\Api\Tutor\PhotoStepController;
use App\Http\Controllers\Api\Tutor\CertificationController;

// Auth Routes
Route::prefix('auth')->group(function () {
   Route::post('login', [AuthController::class, 'login']);
   Route::middleware('auth:api')->group(function () {
       Route::post('logout', [AuthController::class, 'logout']);
       Route::post('refresh', [AuthController::class, 'refresh']);
       Route::get('me', [AuthController::class, 'me']);
   });
});

// Password Reset Routes
Route::prefix('auth')->group(function () {
   Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink']);
   Route::post('reset-password', [PasswordResetController::class, 'reset']);
   Route::get('/reset-password/{token}', function ($token) {
       return view('auth.reset-password', ['token' => $token]);
   })->name('password.reset');
});

// User stats API
Route::middleware('auth:api')->get('/user-stats', [AuthController::class, 'userStats']);

// Super Admin Routes
Route::prefix('admin')->middleware(['auth:api', 'super.admin'])->group(function () {
   Route::get('dashboard', [AdminController::class, 'dashboard']);
   Route::get('users', [AdminController::class, 'users']);
   Route::get('roles', [AdminController::class, 'roles']);
});

// Newsletter
Route::post('/newsletters', [NewsleterController::class, 'store']);
Route::get('/newsletters', [NewsleterController::class, 'index']);
Route::middleware('auth:api')->post('/newsletters/send-emails', [NewsleterController::class, 'sendEmails']);

// Tutor Registration Steps
Route::prefix('tutors')->middleware('auth:api')->group(function () {
   Route::post('/about', [AboutStepController::class, 'store']); 
   Route::put('/about/{id}', [AboutStepController::class, 'update']);

   Route::post('/photo', [PhotoStepController::class, 'store_photo']);

   // Route for certifications
   Route::post('/certifications', [CertificationController::class, 'store_certifications']);

});