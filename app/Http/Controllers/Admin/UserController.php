<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\Setting;
use App\Models\User;
use App\Support\AppSettings;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // ===== Filters (sesuai blade) =====
        $q      = trim((string) $request->query('q', ''));
        $role   = (string) $request->query('role', '');
        $active = (string) $request->query('active', ''); // '', '1', '0'

        // ===== Sorting (sesuai blade) =====
        $sort = (string) $request->query('sort', 'created_at');
        $dir  = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // Kolom sorting yang aman (whitelist)
        $allowedSort = [
            'created_at',
            'name',
            'email',
            'username',
            'nik',
            'phone',
            'role',
            'active',
            'intern_status',
            'internship_start_date',
            'internship_end_date',
            'attended_days', // alias from withCount
        ];

        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'created_at';
        }

        $usersQuery = User::query()
            ->withCount([
                'attendances as attended_days' => function ($query) {
                    $query->where('is_fake_gps', false);
                    $query->select(DB::raw('count(distinct `date`)'));
                },
            ]);

        // ===== Apply search =====
        if ($q !== '') {
            $usersQuery->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        // ===== Apply role filter =====
        if ($role !== '' && in_array($role, ['intern', 'admin'], true)) {
            $usersQuery->where('role', $role);
        }

        // ===== Apply active filter =====
        if ($active === '1' || $active === '0') {
            $usersQuery->where('active', (int) $active);
        }

        // ===== Apply sorting =====
        // NOTE: attended_days itu alias dari withCount, bisa di-orderBy langsung.
        $usersQuery->orderBy($sort, $dir);

        // Secondary sort biar stabil (opsional)
        if ($sort !== 'name') {
            $usersQuery->orderBy('name', 'asc');
        }

        $users = $usersQuery
            ->paginate(25)
            ->withQueryString();

        // ===== Scoring settings =====
        $scoring = [
            'points_per_attendance' => AppSettings::getInt(AppSettings::SCORE_POINTS_PER_ATTENDANCE, 4),
            'max_score' => AppSettings::getInt(AppSettings::SCORE_MAX, 100),
        ];

        // ===== Compute score (tetap sama) =====
        $users->getCollection()->transform(function (User $user) use ($scoring) {
            if (($user->role ?? 'intern') !== 'intern') {
                $user->computed_score = null;
                $user->computed_score_is_override = false;
                $user->computed_score_attended_days = null;
                $user->computed_score_expected_days = null;
                $user->computed_score_subtitle = null;
                return $user;
            }

            $maxScore = (int) ($scoring['max_score'] ?? 100);
            $points = (int) ($scoring['points_per_attendance'] ?? 4);
            $attendedDays = (int) ($user->attended_days ?? 0);

            $expectedDays = null;
            if (!empty($user->internship_start_date) && !empty($user->internship_end_date)) {
                $start = Carbon::parse($user->internship_start_date)->startOfDay();
                $end = Carbon::parse($user->internship_end_date)->startOfDay();

                if ($end->greaterThanOrEqualTo($start)) {
                    $expectedDays = 0;
                    $cursor = $start->copy();
                    while ($cursor->lessThanOrEqualTo($end)) {
                        if ($cursor->isWeekday()) {
                            $expectedDays++;
                        }
                        $cursor->addDay();
                    }

                    if ($expectedDays > 500) {
                        $expectedDays = null;
                    }
                }
            }

            if ($expectedDays !== null && $expectedDays > 0) {
                $autoScore = min($maxScore, min($attendedDays, $expectedDays) * $points);
            } else {
                $autoScore = min($maxScore, $attendedDays * $points);
            }

            $finalScore = ($user->score_override !== null) ? (int) $user->score_override : $autoScore;

            $user->computed_score = $finalScore;
            $user->computed_score_is_override = ($user->score_override !== null);
            $user->computed_score_attended_days = $attendedDays;
            $user->computed_score_expected_days = $expectedDays;

            if ($user->computed_score_is_override) {
                $user->computed_score_subtitle = 'Override';
            } else {
                if ($expectedDays !== null) {
                    $user->computed_score_subtitle = "Auto ({$attendedDays}/{$expectedDays} hari)";
                } else {
                    $user->computed_score_subtitle = "Auto ({$attendedDays} hari)";
                }
            }

            return $user;
        });

        return view('admin.users.list', [
            'users' => $users,
            'scoring' => $scoring,
        ]);
    }

    public function security(Request $request)
    {
        $registrationSecurityEnabled = AppSettings::getString(AppSettings::REGISTRATION_ADMIN_CODE_HASH, '') !== '';

        return view('admin.users.security', [
            'registrationSecurityEnabled' => $registrationSecurityEnabled,
        ]);
    }

    public function scoring(Request $request)
    {
        $scoring = [
            'points_per_attendance' => AppSettings::getInt(AppSettings::SCORE_POINTS_PER_ATTENDANCE, 4),
            'max_score' => AppSettings::getInt(AppSettings::SCORE_MAX, 100),
        ];

        return view('admin.users.scoring', [
            'scoring' => $scoring,
        ]);
    }

    public function create(Request $request)
    {
        $locations = Location::query()->orderBy('name')->get();

        return view('admin.users.form', [
            'editUser' => null,
            'locations' => $locations,
        ]);
    }

    public function edit(Request $request, User $user)
    {
        $locations = Location::query()->orderBy('name')->get();

        return view('admin.users.form', [
            'editUser' => $user,
            'locations' => $locations,
        ]);
    }

    public function updateRegistrationSecurity(Request $request)
    {
        $validated = $request->validate([
            'registration_code' => ['required', 'string', 'min:6', 'max:100', 'confirmed'],
        ]);

        Setting::setValue(AppSettings::REGISTRATION_ADMIN_CODE_HASH, Hash::make((string) $validated['registration_code']));

        return back()->with('status', 'Kode registrasi berhasil diperbarui.');
    }

    public function disableRegistrationSecurity(Request $request)
    {
        Setting::setValue(AppSettings::REGISTRATION_ADMIN_CODE_HASH, '');

        return back()->with('status', 'Registrasi berhasil dinonaktifkan.');
    }

    public function completeInternship(Request $request, User $user)
    {
        if (($user->role ?? 'intern') !== 'intern') {
            return back()->withErrors(['action' => 'Hanya user role intern yang bisa diselesaikan magangnya.']);
        }

        $validated = $request->validate([
            'aspect_1' => ['required', 'integer', 'min:0', 'max:100'],
            'aspect_2' => ['required', 'integer', 'min:0', 'max:100'],
            'aspect_3' => ['required', 'integer', 'min:0', 'max:100'],
            'aspect_4' => ['required', 'integer', 'min:0', 'max:100'],
            'aspect_5' => ['required', 'integer', 'min:0', 'max:100'],
            'signatory_name' => ['nullable', 'string', 'max:255'],
            'signatory_title' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($user->internship_start_date) || empty($user->internship_end_date)) {
            return back()->withErrors([
                'action' => 'Tanggal magang belum lengkap. Isi tanggal mulai & selesai magang dulu.',
            ]);
        }

        $start = Carbon::parse($user->internship_start_date)->startOfDay();
        $end = Carbon::parse($user->internship_end_date)->startOfDay();
        if ($end->lessThan($start)) {
            return back()->withErrors([
                'action' => 'Tanggal selesai magang tidak boleh sebelum tanggal mulai.',
            ]);
        }

        $expectedDays = 0;
        $cursor = $start->copy();
        while ($cursor->lessThanOrEqualTo($end)) {
            if ($cursor->isWeekday()) {
                $expectedDays++;
            }
            $cursor->addDay();
        }

        $attendedDays = (int) Attendance::query()
            ->where('user_id', $user->id)
            ->where('is_fake_gps', false)
            ->select(DB::raw('count(distinct `date`) as c'))
            ->value('c');

        $baseScore = (int) round(((int) $validated['aspect_1']
            + (int) $validated['aspect_2']
            + (int) $validated['aspect_3']
            + (int) $validated['aspect_4']
            + (int) $validated['aspect_5']) / 5);

        $ratio = ($expectedDays > 0) ? min(1.0, $attendedDays / $expectedDays) : 1.0;
        $finalScore = (int) round($baseScore * $ratio);
        $finalScore = max(0, min(100, $finalScore));

        $evaluation = [
            'aspects' => [
                (int) $validated['aspect_1'],
                (int) $validated['aspect_2'],
                (int) $validated['aspect_3'],
                (int) $validated['aspect_4'],
                (int) $validated['aspect_5'],
            ],
            'base_score' => $baseScore,
            'attended_days' => $attendedDays,
            'expected_days' => $expectedDays,
            'ratio' => $ratio,
            'final_score' => $finalScore,
        ];

        $note = "Evaluasi akhir. Base={$baseScore}; Kehadiran={$attendedDays}/{$expectedDays}; Final={$finalScore}.";

        $user->forceFill([
            'intern_status' => 'tamat',
            'score_override' => $finalScore,
            'score_override_note' => $note,
            'final_evaluation' => $evaluation,
            'final_evaluation_at' => now(),
            'certificate_signatory_name' => ($validated['signatory_name'] ?? null) ?: ($user->certificate_signatory_name ?? null),
            'certificate_signatory_title' => ($validated['signatory_title'] ?? null) ?: ($user->certificate_signatory_title ?? null),
        ])->save();

        return back()->with('status', 'Magang berhasil diselesaikan dan nilai akhir tersimpan.');
    }

    public function certificatePdf(Request $request, User $user)
    {
        if (($user->role ?? 'intern') !== 'intern') {
            abort(404);
        }
        if (($user->intern_status ?? 'aktif') !== 'tamat') {
            return back()->withErrors(['action' => 'Sertifikat hanya tersedia jika status intern sudah TAMAT.']);
        }

        $user->loadMissing(['internshipLocation']);

        $signatoryName = (string) ($user->certificate_signatory_name
            ?? AppSettings::getString(AppSettings::CERTIFICATE_DEFAULT_SIGNATORY_NAME, 'Kepala Dinas'));
        $signatoryTitle = (string) ($user->certificate_signatory_title
            ?? AppSettings::getString(AppSettings::CERTIFICATE_DEFAULT_SIGNATORY_TITLE, 'Kepala Dinas'));

        $issuedAt = now();
        $certificateNo = 'MAGANG-' . $user->id . '/' . $issuedAt->format('Y');

        $html = view('admin.users.certificate_pdf', [
            'user' => $user,
            'issuedAt' => $issuedAt,
            'certificateNo' => $certificateNo,
            'signatoryName' => $signatoryName,
            'signatoryTitle' => $signatoryTitle,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'sertifikat-magang-' . Str::slug((string) $user->name) . '-' . $issuedAt->format('Ymd') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $scoring = [
            'points_per_attendance' => AppSettings::getInt(AppSettings::SCORE_POINTS_PER_ATTENDANCE, 4),
            'max_score' => AppSettings::getInt(AppSettings::SCORE_MAX, 100),
        ];

        $users = User::query()
            ->withCount([
                'attendances as attended_days' => function ($query) {
                    $query->where('is_fake_gps', false);
                    $query->select(DB::raw('count(distinct `date`)'));
                },
            ])
            ->orderBy('name')
            ->get();

        $users->transform(function (User $user) use ($scoring) {
            if (($user->role ?? 'intern') !== 'intern') {
                $user->computed_score = null;
                $user->computed_score_is_override = false;
                $user->computed_score_attended_days = null;
                $user->computed_score_expected_days = null;
                return $user;
            }

            $maxScore = (int) ($scoring['max_score'] ?? 100);
            $points = (int) ($scoring['points_per_attendance'] ?? 4);
            $attendedDays = (int) ($user->attended_days ?? 0);

            $expectedDays = null;
            if (!empty($user->internship_start_date) && !empty($user->internship_end_date)) {
                $start = Carbon::parse($user->internship_start_date)->startOfDay();
                $end = Carbon::parse($user->internship_end_date)->startOfDay();

                if ($end->greaterThanOrEqualTo($start)) {
                    $expectedDays = 0;
                    $cursor = $start->copy();
                    while ($cursor->lessThanOrEqualTo($end)) {
                        if ($cursor->isWeekday()) {
                            $expectedDays++;
                        }
                        $cursor->addDay();
                    }

                    if ($expectedDays > 500) {
                        $expectedDays = null;
                    }
                }
            }

            if ($expectedDays !== null && $expectedDays > 0) {
                $autoScore = min($maxScore, min($attendedDays, $expectedDays) * $points);
            } else {
                $autoScore = min($maxScore, $attendedDays * $points);
            }
            $finalScore = ($user->score_override !== null) ? (int) $user->score_override : $autoScore;

            $user->computed_score = $finalScore;
            $user->computed_score_is_override = ($user->score_override !== null);
            $user->computed_score_attended_days = $attendedDays;
            $user->computed_score_expected_days = $expectedDays;
            return $user;
        });

        $generatedAt = now();
        $html = view('admin.users.export_pdf', [
            'generatedAt' => $generatedAt,
            'users' => $users,
            'scoring' => $scoring,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'laporan-users-' . now()->format('Ymd-His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $scoring = [
            'points_per_attendance' => AppSettings::getInt(AppSettings::SCORE_POINTS_PER_ATTENDANCE, 4),
            'max_score' => AppSettings::getInt(AppSettings::SCORE_MAX, 100),
        ];

        $users = User::query()
            ->withCount([
                'attendances as attended_days' => function ($query) {
                    $query->where('is_fake_gps', false);
                    $query->select(DB::raw('count(distinct `date`)'));
                },
            ])
            ->orderBy('name')
            ->get();

        $users->transform(function (User $user) use ($scoring) {
            if (($user->role ?? 'intern') !== 'intern') {
                $user->computed_score = null;
                $user->computed_score_is_override = false;
                $user->computed_score_attended_days = null;
                $user->computed_score_expected_days = null;
                return $user;
            }

            $maxScore = (int) ($scoring['max_score'] ?? 100);
            $points = (int) ($scoring['points_per_attendance'] ?? 4);
            $attendedDays = (int) ($user->attended_days ?? 0);

            $expectedDays = null;
            if (!empty($user->internship_start_date) && !empty($user->internship_end_date)) {
                $start = Carbon::parse($user->internship_start_date)->startOfDay();
                $end = Carbon::parse($user->internship_end_date)->startOfDay();

                if ($end->greaterThanOrEqualTo($start)) {
                    $expectedDays = 0;
                    $cursor = $start->copy();
                    while ($cursor->lessThanOrEqualTo($end)) {
                        if ($cursor->isWeekday()) {
                            $expectedDays++;
                        }
                        $cursor->addDay();
                    }

                    if ($expectedDays > 500) {
                        $expectedDays = null;
                    }
                }
            }

            if ($expectedDays !== null && $expectedDays > 0) {
                $autoScore = min($maxScore, min($attendedDays, $expectedDays) * $points);
            } else {
                $autoScore = min($maxScore, $attendedDays * $points);
            }
            $finalScore = ($user->score_override !== null) ? (int) $user->score_override : $autoScore;

            $user->computed_score = $finalScore;
            $user->computed_score_is_override = ($user->score_override !== null);
            $user->computed_score_attended_days = $attendedDays;
            $user->computed_score_expected_days = $expectedDays;
            return $user;
        });

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Sistem Absensi & Buku Tamu')
            ->setTitle('Laporan Users');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Users');
        $sheet->fromArray([
            ['Laporan Users'],
            ['Generated At', now()->format('Y-m-d H:i:s')],
            [],
            ['Nama', 'Username', 'Email', 'Role', 'Aktif', 'Intern Status', 'Hadir (hari)', 'Nilai', 'Override?', 'Catatan', 'Magang Mulai', 'Magang Selesai'],
        ]);

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4:L4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->freezePane('A5');

        $row = 5;
        foreach ($users as $u) {
            $role = (string) ($u->role ?? 'intern');
            $isIntern = $role === 'intern';

            $note = '';
            if ($isIntern) {
                $note = $u->computed_score_is_override ? ((string) ($u->score_override_note ?? 'Override')) : 'Auto';
            }

            $sheet->fromArray([
                (string) $u->name,
                (string) $u->username,
                (string) $u->email,
                strtoupper($role),
                ((bool) ($u->active ?? true)) ? 'Ya' : 'Tidak',
                $isIntern ? (string) ($u->intern_status ?? 'aktif') : '-',
                $isIntern ? (int) ($u->attended_days ?? 0) : '-',
                $isIntern && $u->computed_score !== null ? (int) $u->computed_score : '-',
                $isIntern ? ($u->computed_score_is_override ? 'Ya' : 'Tidak') : '-',
                $note,
                $isIntern ? ((string) ($u->internship_start_date ?? '')) : '',
                $isIntern ? ((string) ($u->internship_end_date ?? '')) : '',
            ], null, "A{$row}");

            if ($isIntern && !empty($u->internship_start_date)) {
                $sheet->setCellValue("K{$row}", ExcelDate::PHPToExcel(Carbon::parse($u->internship_start_date)->startOfDay()));
            }
            if ($isIntern && !empty($u->internship_end_date)) {
                $sheet->setCellValue("L{$row}", ExcelDate::PHPToExcel(Carbon::parse($u->internship_end_date)->startOfDay()));
            }
            $row++;
        }

        $lastRow = max(4, $row - 1);
        $sheet->setAutoFilter("A4:L{$lastRow}");
        if ($lastRow >= 5) {
            $sheet->getStyle("J5:J{$lastRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("K5:L{$lastRow}")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'laporan-users-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16', Rule::unique('users', 'nik')],
            'phone' => ['required', 'string', 'max:30'],
            'username' => ['required', 'string', 'lowercase', 'alpha_dash', 'max:50', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', 'string', Rule::in(['admin', 'intern'])],
            'active' => ['required', 'boolean'],
            'intern_status' => ['nullable', 'string', Rule::in(['aktif', 'tamat'])],
            'internship_start_date' => ['nullable', 'date'],
            'internship_end_date' => ['nullable', 'date', 'after_or_equal:internship_start_date'],
            'internship_location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'score_override' => ['nullable', 'integer', 'min:0', 'max:100'],
            'score_override_note' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $internStatus = ($validated['role'] === 'intern')
            ? ($validated['intern_status'] ?? 'aktif')
            : 'aktif';

        User::query()->create([
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'phone' => $validated['phone'],
            'username' => Str::lower($validated['username']),
            'email' => $validated['email'],
            'role' => $validated['role'],
            'active' => (bool) $validated['active'],
            'intern_status' => $internStatus,
            'internship_start_date' => ($validated['role'] === 'intern') ? ($validated['internship_start_date'] ?? null) : null,
            'internship_end_date' => ($validated['role'] === 'intern') ? ($validated['internship_end_date'] ?? null) : null,
            'internship_location_id' => ($validated['role'] === 'intern') ? ($validated['internship_location_id'] ?? null) : null,
            'score_override' => ($validated['role'] === 'intern') ? ($validated['score_override'] ?? null) : null,
            'score_override_note' => ($validated['role'] === 'intern') ? ($validated['score_override_note'] ?? null) : null,
            'password' => $validated['password'],
        ]);

        return back()->with('status', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16', Rule::unique('users', 'nik')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:30'],
            'username' => ['required', 'string', 'lowercase', 'alpha_dash', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'intern'])],
            'active' => ['required', 'boolean'],
            'intern_status' => ['nullable', 'string', Rule::in(['aktif', 'tamat'])],
            'internship_start_date' => ['nullable', 'date'],
            'internship_end_date' => ['nullable', 'date', 'after_or_equal:internship_start_date'],
            'internship_location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'score_override' => ['nullable', 'integer', 'min:0', 'max:100'],
            'score_override_note' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (($request->user()?->id === $user->id) && ($validated['role'] ?? null) !== ($user->role ?? 'intern')) {
            return back()->withErrors([
                'role' => 'Tidak bisa mengubah role akun sendiri.',
            ]);
        }

        if (($request->user()?->id === $user->id) && ((bool) ($validated['active'] ?? true) === false)) {
            return back()->withErrors([
                'active' => 'Tidak bisa menonaktifkan akun sendiri.',
            ]);
        }

        $payload = [
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'phone' => $validated['phone'],
            'username' => Str::lower($validated['username']),
            'email' => $validated['email'],
            'role' => $validated['role'],
            'active' => (bool) $validated['active'],
        ];

        if (($validated['role'] ?? 'intern') === 'intern') {
            $payload['intern_status'] = $validated['intern_status'] ?? ($user->intern_status ?? 'aktif');
            $payload['internship_start_date'] = $validated['internship_start_date'] ?? null;
            $payload['internship_end_date'] = $validated['internship_end_date'] ?? null;
            $payload['internship_location_id'] = $validated['internship_location_id'] ?? null;
            $payload['score_override'] = $validated['score_override'] ?? null;
            $payload['score_override_note'] = $validated['score_override_note'] ?? null;
        } else {
            $payload['intern_status'] = 'aktif';
            $payload['internship_start_date'] = null;
            $payload['internship_end_date'] = null;
            $payload['internship_location_id'] = null;
            $payload['score_override'] = null;
            $payload['score_override_note'] = null;
        }

        if (!empty($validated['password'] ?? '')) {
            $payload['password'] = $validated['password'];
        }

        $user->forceFill($payload)->save();

        return redirect()->route('admin.users.index')->with('status', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()?->id === $user->id) {
            return back()->withErrors([
                'delete' => 'Tidak bisa menghapus akun sendiri.',
            ]);
        }

        $user->delete();

        return back()->with('status', 'User berhasil dihapus.');
    }

    public function updateRole(Request $request, User $user)
    {
        if ($request->user()?->id === $user->id) {
            return back()->withErrors([
                'role' => 'Tidak bisa mengubah role akun sendiri.',
            ]);
        }

        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(['admin', 'intern'])],
        ]);

        $user->forceFill([
            'role' => $validated['role'],
        ])->save();

        return back()->with('status', 'Role user berhasil diperbarui.');
    }

    public function updateActive(Request $request, User $user)
    {
        if ($request->user()?->id === $user->id) {
            return back()->withErrors([
                'active' => 'Tidak bisa menonaktifkan akun sendiri.',
            ]);
        }

        $validated = $request->validate([
            'active' => ['required', 'boolean'],
        ]);

        $user->forceFill([
            'active' => (bool) $validated['active'],
        ])->save();

        return back()->with('status', 'Status user berhasil diperbarui.');
    }

    public function updateScoringSettings(Request $request)
    {
        $validated = $request->validate([
            'points_per_attendance' => ['required', 'integer', 'min:0', 'max:100'],
            'max_score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        Setting::setValue(AppSettings::SCORE_POINTS_PER_ATTENDANCE, (string) $validated['points_per_attendance']);
        Setting::setValue(AppSettings::SCORE_MAX, (string) $validated['max_score']);

        return back()->with('status', 'Aturan penilaian berhasil disimpan.');
    }
}
