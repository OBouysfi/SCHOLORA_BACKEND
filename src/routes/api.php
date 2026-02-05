<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\NewsleterController;
use App\Http\Controllers\Api\Tutor\TutorRegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\Student\StudentRegistrationController;
use App\Http\Controllers\Api\Admin\PricingPackController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);  

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
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
Route::prefix('admin')->group(function () {
    Route::get('pricing-packs', [PricingPackController::class, 'index']);
    Route::post('pricing-packs', [PricingPackController::class, 'store']);
    Route::put('pricing-packs/{id}', [PricingPackController::class, 'update']);
    Route::delete('pricing-packs/{id}', [PricingPackController::class, 'destroy']);
});
// Newsletter
Route::post('/newsletters', [NewsleterController::class, 'store']);
Route::get('/newsletters', [NewsleterController::class, 'index']);
Route::middleware('auth:api')->post('/newsletters/send-emails', [NewsleterController::class, 'sendEmails']);
// Contact Support
Route::post('/contact', [ContactController::class, 'store']);

Route::prefix('tutor-registration')->group(function () {
    Route::post('/about', [TutorRegistrationController::class, 'saveAboutStep']);
    Route::get('/draft/{email}', [TutorRegistrationController::class, 'getTutorDraft']);
    Route::post('/photo', [TutorRegistrationController::class, 'savePhotoStep']);
    Route::post('/certification', [TutorRegistrationController::class, 'saveCertificationStep']);
    Route::post('/education', [TutorRegistrationController::class, 'saveEducationStep']);
    Route::post('/description', [TutorRegistrationController::class, 'saveDescriptionStep']);
    Route::post('/video', [TutorRegistrationController::class, 'saveVideoStep']);
    Route::post('/availability', [TutorRegistrationController::class, 'saveAvailabilityStep']);
    Route::post('/pricing', [TutorRegistrationController::class, 'savePricingStep']);
    Route::post('/submit', [TutorRegistrationController::class, 'submitProfile']);
});

Route::prefix('students')->group(function () {
    Route::post('/register', [StudentRegistrationController::class, 'store']);
    Route::post('/login', [StudentAuthController::class, 'login']);
});

