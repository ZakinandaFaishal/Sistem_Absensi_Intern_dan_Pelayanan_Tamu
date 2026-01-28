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
    ->middleware(['auth', 'verified', 'role:super_admin,admin_dinas', 'admin_dinas_has_dinas'])
    ->name('kiosk.absensi');

Route::post('/kiosk/token', [KioskController::class, 'token'])
    ->middleware(['auth', 'verified', 'role:super_admin,admin_dinas', 'admin_dinas_has_dinas', 'throttle:120,1'])
    ->name('kiosk.token');

Route::get('/presensi/scan', [AttendanceScanController::class, 'show'])
    ->middleware('auth')
    ->name('attendance.scan.show');

Route::post('/presensi/scan', [AttendanceScanController::class, 'store'])
    ->middleware('auth')
    ->name('attendance.scan.store');

Route::view('/presensi/scan-qr', 'attendance.qr')
    ->middleware(['auth', 'verified', 'role:intern,super_admin,admin_dinas'])
    ->name('attendance.qr');

// =====================
// GUEST (PUBLIC)
// =====================
Route::get('/tamu', [GuestVisitController::class, 'create'])->name('guest.create');
Route::post('/tamu', [GuestVisitController::class, 'store'])->name('guest.store');

Route::get('/tamu/{visit}/survey', [GuestSurveyController::class, 'show'])->name('guest.survey.show');
Route::post('/tamu/{visit}/survey', [GuestSurveyController::class, 'store'])->name('guest.survey.store');

// ✅ ROUTE YANG KURANG (untuk redirect setelah submit survey)
Route::get('/tamu/{visit}/survey/thanks', [GuestSurveyController::class, 'thanks'])->name('guest.thanks');

Route::get('/tamu/active', [GuestVisitController::class, 'active'])->name('guest.active');

Route::get('/admin', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $role = Auth::user()->role ?? null;
    if ($role === 'admin') {
        $role = 'super_admin';
    }

    if (!in_array($role, ['super_admin', 'admin_dinas'], true)) {
        return redirect()->route('dashboard');
    }

    if ($role === 'admin_dinas') {
        return redirect()->route('admin.attendance.manage');
    }

    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('admin.home');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'role:super_admin,admin_dinas', 'admin_dinas_has_dinas'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Buku tamu admin (super_admin + admin_dinas, tetapi data dibatasi per dinas di controller)
        Route::get('/tamu', [GuestVisitController::class, 'index'])->name('guest.index');
        Route::post('/tamu/{visit}/complete', [GuestVisitController::class, 'complete'])->name('guest.complete');

        // Shared admin area (super_admin + admin_dinas): only scoped resources
        Route::get('/presensi/pengaturan', [AdminAttendanceController::class, 'manage'])->name('attendance.manage');
        Route::get('/presensi/aturan', [AdminAttendanceController::class, 'rules'])->name('attendance.rules');
        Route::post('/presensi/settings', [AdminAttendanceController::class, 'updateSettings'])->name('attendance.settings');

        // Survey & Users (super_admin + admin_dinas; data & akses dibatasi di controller)
        Route::get('/survey', [AdminSurveyController::class, 'index'])->name('survey.index');
        Route::get('/survey/ikm', [AdminSurveyController::class, 'ikm'])->name('survey.ikm');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/complete-internship', [AdminUserController::class, 'completeInternship'])->name('users.complete-internship');
        Route::get('/users/{user}/certificate.pdf', [AdminUserController::class, 'certificatePdf'])->name('users.certificate.pdf');

        // ======================================================
        // EXPORT - USERS / SURVEY / BUKU TAMU / PRESENSI
        // (super_admin + admin_dinas; data dibatasi per dinas di controller)
        // ======================================================
        Route::get('/users/export/excel', [AdminUserController::class, 'exportExcel'])
            ->name('users.export.excel');

        Route::get('/users/export/pdf', [AdminUserController::class, 'exportPdf'])
            ->name('users.export.pdf');

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

        Route::get('/tamu/export/excel', [GuestVisitController::class, 'exportExcel'])
            ->name('guest.export.excel');

        Route::get('/tamu/export/pdf', [GuestVisitController::class, 'exportPdf'])
            ->name('guest.export.pdf');

        Route::get('/presensi/export/excel', [AdminAttendanceController::class, 'exportExcel'])
            ->name('attendance.export.excel');

        Route::get('/presensi/export/pdf', [AdminAttendanceController::class, 'exportPdf'])
            ->name('attendance.export.pdf');

        // super_admin-only area
        Route::middleware(['role:super_admin'])->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // Lokasi / Dinas (super_admin only)
            Route::get('/presensi/lokasi', [AdminAttendanceController::class, 'locations'])->name('attendance.locations');
            Route::post('/presensi/locations', [AdminAttendanceController::class, 'storeLocation'])->name('attendance.locations.store');
            Route::get('/presensi/locations/{location}/edit', [AdminAttendanceController::class, 'editLocation'])->name('attendance.locations.edit');
            Route::patch('/presensi/locations/{location}', [AdminAttendanceController::class, 'updateLocation'])->name('attendance.locations.update');
            Route::delete('/presensi/locations/{location}', [AdminAttendanceController::class, 'destroyLocation'])->name('attendance.locations.destroy');

            // ======================================================
            // EXPORT - DASHBOARD (SELURUH DATA)
            // ======================================================
            Route::get('/export/excel', [AdminDashboardController::class, 'exportExcel'])
                ->name('export.excel');

            Route::get('/export/pdf', [AdminDashboardController::class, 'exportPdf'])
                ->name('export.pdf');

            // ======================================================
            // ROUTE UTAMA ADMIN
            // ======================================================
            Route::get('/presensi', [AdminAttendanceController::class, 'index'])->name('attendance.index');

            Route::get('/users/keamanan-registrasi', [AdminUserController::class, 'security'])->name('users.security');
            Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');

            Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
            Route::post('/users/registration-security', [AdminUserController::class, 'updateRegistrationSecurity'])->name('users.registration-security');
            Route::delete('/users/registration-security', [AdminUserController::class, 'disableRegistrationSecurity'])->name('users.registration-security.disable');
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
            Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
        });
    });

Route::middleware(['auth', 'verified', 'role:intern'])
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
