<?php

use App\Http\Controllers\AttendanceScanController;
use App\Http\Controllers\GuestSurveyController;
use App\Http\Controllers\GuestVisitController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('kiosk.index');
});

Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk.index');
Route::get('/kiosk/absensi', [KioskController::class, 'absensi'])->name('kiosk.absensi');
Route::post('/kiosk/token', [KioskController::class, 'token'])->middleware('throttle:120,1')->name('kiosk.token');

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
