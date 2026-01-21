<?php

use App\Http\Controllers\AttendanceScanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\SurveyController as AdminSurveyController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Intern\AttendanceController as InternAttendanceController;
use App\Http\Controllers\GuestSurveyController;
use App\Http\Controllers\GuestVisitController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('kiosk.display');
});

Route::get('/kiosk', function () {
    return redirect()->route('kiosk.display');
})->name('kiosk.index');

Route::get('/kiosk/display', [KioskController::class, 'display'])
    ->middleware(['auth', 'verified'])
    ->name('kiosk.display');

Route::get('/kiosk/absensi', [KioskController::class, 'absensi'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('kiosk.absensi');

Route::post('/kiosk/token', [KioskController::class, 'token'])
    ->middleware(['auth', 'verified', 'role:admin', 'throttle:120,1'])
    ->name('kiosk.token');

Route::get('/presensi/scan', [AttendanceScanController::class, 'show'])
    ->middleware('auth')
    ->name('attendance.scan.show');

Route::post('/presensi/scan', [AttendanceScanController::class, 'store'])
    ->middleware('auth')
    ->name('attendance.scan.store');

Route::view('/presensi/scan-qr', 'attendance.qr')
    ->middleware(['auth', 'verified', 'role:intern,admin'])
    ->name('attendance.qr');

Route::get('/tamu', [GuestVisitController::class, 'create'])->name('guest.create');
Route::post('/tamu', [GuestVisitController::class, 'store'])->name('guest.store');
Route::get('/tamu/{visit}/survey', [GuestSurveyController::class, 'show'])->name('guest.survey.show');
Route::post('/tamu/{visit}/survey', [GuestSurveyController::class, 'store'])->name('guest.survey.store');
Route::get('/tamu/active', [GuestVisitController::class, 'active'])->name('guest.active');



Route::get('/admin', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    if ((Auth::user()->role ?? null) !== 'admin') {
        return redirect()->route('dashboard');
    }

    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('admin.home');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin/tamu', [GuestVisitController::class, 'index'])->name('admin.guest.index');
    Route::post('/admin/tamu/{visit}/complete', [GuestVisitController::class, 'complete'])->name('admin.guest.complete');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // ======================================================
        // EXPORT - DASHBOARD (SELURUH DATA)
        // ======================================================
        Route::get('/export/excel', [AdminDashboardController::class, 'exportExcel'])
            ->name('export.excel');

        Route::get('/export/pdf', [AdminDashboardController::class, 'exportPdf'])
            ->name('export.pdf');

        // ======================================================
        // EXPORT - USERS (LAPORAN)
        // ======================================================
        Route::get('/users/export/excel', [AdminUserController::class, 'exportExcel'])
            ->name('users.export.excel');

        Route::get('/users/export/pdf', [AdminUserController::class, 'exportPdf'])
            ->name('users.export.pdf');

        // ======================================================
        // EXPORT - SURVEY (LAPORAN)
        // ======================================================
        Route::get('/survey/export/excel', [AdminSurveyController::class, 'exportExcel'])
            ->name('survey.export.excel');

        Route::get('/survey/export/pdf', [AdminSurveyController::class, 'exportPdf'])
            ->name('survey.export.pdf');

        Route::get('/survey/export/ikm.pdf', [AdminSurveyController::class, 'exportIkmPdf'])
            ->name('survey.export.ikm.pdf');

        Route::get('/survey/export/ikm.csv', [AdminSurveyController::class, 'exportIkmCsv'])
            ->name('survey.export.ikm.csv');

        Route::get('/survey/export/detail.csv', [AdminSurveyController::class, 'exportDetailCsv'])
            ->name('survey.export.detail.csv');

        // ======================================================
        // EXPORT - BUKU TAMU (LAPORAN)
        // ======================================================
        Route::get('/tamu/export/excel', [GuestVisitController::class, 'exportExcel'])
            ->name('guest.export.excel');

        Route::get('/tamu/export/pdf', [GuestVisitController::class, 'exportPdf'])
            ->name('guest.export.pdf');

        // ======================================================
        // EXPORT - PRESENSI (LAPORAN)
        // ======================================================
        Route::get('/presensi/export/excel', [AdminAttendanceController::class, 'exportExcel'])
            ->name('attendance.export.excel');

        Route::get('/presensi/export/pdf', [AdminAttendanceController::class, 'exportPdf'])
            ->name('attendance.export.pdf');

        // ======================================================
        // ROUTE UTAMA ADMIN
        // ======================================================
        Route::get('/presensi', [AdminAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/presensi/aturan', [AdminAttendanceController::class, 'rules'])->name('attendance.rules');
        Route::get('/presensi/lokasi', [AdminAttendanceController::class, 'locations'])->name('attendance.locations');
        Route::post('/presensi/settings', [AdminAttendanceController::class, 'updateSettings'])->name('attendance.settings');
        Route::post('/presensi/locations', [AdminAttendanceController::class, 'storeLocation'])->name('attendance.locations.store');
        Route::patch('/presensi/locations/{location}', [AdminAttendanceController::class, 'updateLocation'])->name('attendance.locations.update');
        Route::delete('/presensi/locations/{location}', [AdminAttendanceController::class, 'destroyLocation'])->name('attendance.locations.destroy');
        Route::post('/presensi/{attendance}/fake-gps', [AdminAttendanceController::class, 'toggleFakeGps'])->name('attendance.fake-gps');
        Route::get('/survey', [AdminSurveyController::class, 'index'])->name('survey.index');
        Route::get('/survey/ikm', [AdminSurveyController::class, 'ikm'])->name('survey.ikm');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');

        Route::get('/users/keamanan-registrasi', [AdminUserController::class, 'security'])->name('users.security');
        Route::get('/users/aturan-penilaian', [AdminUserController::class, 'scoring'])->name('users.scoring');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');

        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::post('/users/registration-security', [AdminUserController::class, 'updateRegistrationSecurity'])->name('users.registration-security');
        Route::delete('/users/registration-security', [AdminUserController::class, 'disableRegistrationSecurity'])->name('users.registration-security.disable');
        Route::post('/users/scoring-settings', [AdminUserController::class, 'updateScoringSettings'])->name('users.scoring.settings');
        Route::post('/users/{user}/complete-internship', [AdminUserController::class, 'completeInternship'])->name('users.complete-internship');
        Route::get('/users/{user}/certificate.pdf', [AdminUserController::class, 'certificatePdf'])->name('users.certificate.pdf');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
        Route::patch('/users/{user}/active', [AdminUserController::class, 'updateActive'])->name('users.active');
    });

Route::middleware(['auth', 'verified', 'role:intern,admin'])
    ->prefix('intern')
    ->name('intern.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return redirect()->route('intern.userProfile');
        })->name('dashboard');

        Route::view('/userProfile', 'intern.userProfile')->name('userProfile');
        Route::get('/presensi', [InternAttendanceController::class, 'index'])->name('attendance.history');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/profile/mahasiswa', function () {
        return view('profile.mahasiswa');
    })->name('profile.mahasiswa');
});

require __DIR__ . '/auth.php';
