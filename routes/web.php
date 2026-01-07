<?php

use App\Http\Controllers\AttendanceScanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\SurveyController as AdminSurveyController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Intern\AttendanceController as InternAttendanceController;
use App\Http\Controllers\GuestSurveyController;
use App\Http\Controllers\GuestVisitController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('kiosk.index');
});

Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk.index');
Route::get('/kiosk/absensi', [KioskController::class, 'absensi'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('kiosk.absensi');
Route::post('/kiosk/token', [KioskController::class, 'token'])
    ->middleware(['auth', 'verified', 'role:admin', 'throttle:120,1'])
    ->name('kiosk.token');

Route::get('/presensi/scan', [AttendanceScanController::class, 'show'])->middleware('auth')->name('attendance.scan.show');
Route::post('/presensi/scan', [AttendanceScanController::class, 'store'])->middleware('auth')->name('attendance.scan.store');

Route::get('/tamu', [GuestVisitController::class, 'create'])->name('guest.create');
Route::post('/tamu', [GuestVisitController::class, 'store'])->name('guest.store');
Route::get('/tamu/{visit}/survey', [GuestSurveyController::class, 'show'])->name('guest.survey.show');
Route::post('/tamu/{visit}/survey', [GuestSurveyController::class, 'store'])->name('guest.survey.store');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin/tamu', [GuestVisitController::class, 'index'])->name('admin.guest.index');
    Route::post('/admin/tamu/{visit}/complete', [GuestVisitController::class, 'complete'])->name('admin.guest.complete');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/presensi', [AdminAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/survey', [AdminSurveyController::class, 'index'])->name('survey.index');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::patch('/users/{user}/active', [AdminUserController::class, 'updateActive'])->name('users.active');
});

Route::middleware(['auth', 'verified', 'role:intern,admin'])->prefix('intern')->name('intern.')->group(function () {
    Route::view('/dashboard', 'intern.dashboard')->name('dashboard');
    Route::get('/presensi', [InternAttendanceController::class, 'index'])->name('attendance.history');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
