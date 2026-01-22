<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRule;
use App\Models\Dinas;
use App\Models\Location;
use App\Models\Setting;
use App\Support\AppSettings;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AttendanceController extends Controller
{
    private function resolveDinasContext(Request $request, bool $fromPost = false): array
    {
        $user = $request->user();
        $isSuperAdmin = in_array(($user?->role ?? null), ['super_admin'], true);
        $isAdminDinas = in_array(($user?->role ?? null), ['admin_dinas'], true);

        if ($isAdminDinas) {
            $dinasId = (int) ($user?->dinas_id ?? 0);
            $dinas = $dinasId > 0 ? Dinas::query()->find($dinasId) : null;
            return [
                'dinasId' => $dinasId,
                'dinas' => $dinas,
                'dinasOptions' => collect(),
                'isSuperAdmin' => false,
            ];
        }

        if ($isSuperAdmin) {
            $dinasOptions = Dinas::query()->orderBy('name')->get();
            $raw = $fromPost ? $request->input('dinas_id') : $request->query('dinas_id');
            $dinasId = $raw !== null && $raw !== '' ? (int) $raw : 0;
            $dinas = $dinasId > 0 ? $dinasOptions->firstWhere('id', $dinasId) : null;

            return [
                'dinasId' => $dinasId,
                'dinas' => $dinas,
                'dinasOptions' => $dinasOptions,
                'isSuperAdmin' => true,
            ];
        }

        abort(403);
    }

    public function index(Request $request)
    {
        if (($request->user()?->role ?? null) !== 'super_admin') {
            abort(403);
        }

        $q = (string) $request->query('q', '');
        $dateFrom = (string) $request->query('date_from', '');
        $dateTo = (string) $request->query('date_to', '');
        $sort = (string) $request->query('sort', 'date');
        $dir = (string) $request->query('dir', 'desc');
        $dir = in_array($dir, ['asc', 'desc'], true) ? $dir : 'desc';

        $baseQuery = Attendance::query()
            ->select('attendances.*')
            ->with(['user', 'location']);

        if ($q !== '') {
            $baseQuery->whereHas('user', function ($userQuery) use ($q) {
                $userQuery->where('name', 'like', '%' . $q . '%');
            });
        }

        if ($dateFrom !== '') {
            $baseQuery->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $baseQuery->whereDate('date', '<=', $dateTo);
        }

        if ($sort === 'name') {
            $baseQuery
                ->join('users', 'users.id', '=', 'attendances.user_id')
                ->orderBy('users.name', $dir)
                ->orderByDesc('date')
                ->orderByDesc('check_in_at');
        } else {
            $baseQuery
                ->orderBy('date', $dir)
                ->orderByDesc('check_in_at');
        }

        $attendances = $baseQuery
            ->paginate(20)
            ->withQueryString();

        return view('admin.attendance.list', [
            'attendances' => $attendances,
            'filters' => [
                'q' => $q,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort' => $sort,
                'dir' => $dir,
            ],
        ]);
    }

    public function manage(Request $request)
    {
        $ctx = $this->resolveDinasContext($request);

        if (($ctx['isSuperAdmin'] ?? false) && (int) ($ctx['dinasId'] ?? 0) <= 0) {
            $firstDinasId = Dinas::query()->orderBy('name')->value('id');
            if ($firstDinasId) {
                return redirect()->route('admin.attendance.manage', ['dinas_id' => $firstDinasId]);
            }
        }

        $dinasId = (int) ($ctx['dinasId'] ?? 0);
        $rule = $dinasId > 0
            ? AttendanceRule::query()->firstOrCreate(['dinas_id' => $dinasId])
            : null;

        $settings = [
            'office_lat' => $rule?->office_lat !== null ? (string) $rule->office_lat : AppSettings::getString(AppSettings::OFFICE_LAT, ''),
            'office_lng' => $rule?->office_lng !== null ? (string) $rule->office_lng : AppSettings::getString(AppSettings::OFFICE_LNG, ''),
            'radius_m' => $rule?->radius_m ?? AppSettings::getInt(AppSettings::RADIUS_M, 50),
            'max_accuracy_m' => $rule?->max_accuracy_m ?? AppSettings::getInt(AppSettings::MAX_ACCURACY_M, 50),
            'checkin_start' => $rule?->checkin_start ?? AppSettings::getString(AppSettings::CHECKIN_START, '08:00'),
            'checkin_end' => $rule?->checkin_end ?? AppSettings::getString(AppSettings::CHECKIN_END, '12:00'),
            'checkout_start' => $rule?->checkout_start ?? AppSettings::getString(AppSettings::CHECKOUT_START, '13:00'),
            'checkout_end' => $rule?->checkout_end ?? AppSettings::getString(AppSettings::CHECKOUT_END, '16:30'),
        ];

        $locations = collect();
        if (($ctx['isSuperAdmin'] ?? false) && $dinasId > 0) {
            $locations = Location::query()
                ->with('dinas')
                ->where('dinas_id', $dinasId)
                ->orderBy('name')
                ->get();
        }

        return view('admin.attendance.manage', [
            'settings' => $settings,
            'locations' => $locations,
            'dinasOptions' => $ctx['dinasOptions'] ?? collect(),
            'activeDinas' => $ctx['dinas'] ?? null,
            'activeDinasId' => (int) ($ctx['dinasId'] ?? 0),
            'isSuperAdmin' => (bool) ($ctx['isSuperAdmin'] ?? false),
            'noDinas' => (bool) (($ctx['isSuperAdmin'] ?? false) && (($ctx['dinasOptions'] ?? collect())->count() === 0)),
        ]);
    }

    public function rules(Request $request)
    {
        $ctx = $this->resolveDinasContext($request);

        if (($ctx['isSuperAdmin'] ?? false) && (int) ($ctx['dinasId'] ?? 0) <= 0) {
            $firstDinasId = Dinas::query()->orderBy('name')->value('id');
            if ($firstDinasId) {
                return redirect()->route('admin.attendance.rules', ['dinas_id' => $firstDinasId]);
            }
        }

        $dinasId = (int) ($ctx['dinasId'] ?? 0);
        $rule = $dinasId > 0
            ? AttendanceRule::query()->firstOrCreate(['dinas_id' => $dinasId])
            : null;

        $settings = [
            'office_lat' => $rule?->office_lat !== null ? (string) $rule->office_lat : AppSettings::getString(AppSettings::OFFICE_LAT, ''),
            'office_lng' => $rule?->office_lng !== null ? (string) $rule->office_lng : AppSettings::getString(AppSettings::OFFICE_LNG, ''),
            'radius_m' => $rule?->radius_m ?? AppSettings::getInt(AppSettings::RADIUS_M, 50),
            'max_accuracy_m' => $rule?->max_accuracy_m ?? AppSettings::getInt(AppSettings::MAX_ACCURACY_M, 50),
            'checkin_start' => $rule?->checkin_start ?? AppSettings::getString(AppSettings::CHECKIN_START, '08:00'),
            'checkin_end' => $rule?->checkin_end ?? AppSettings::getString(AppSettings::CHECKIN_END, '12:00'),
            'checkout_start' => $rule?->checkout_start ?? AppSettings::getString(AppSettings::CHECKOUT_START, '13:00'),
            'checkout_end' => $rule?->checkout_end ?? AppSettings::getString(AppSettings::CHECKOUT_END, '16:30'),
        ];

        return view('admin.attendance.rules', [
            'settings' => $settings,
            'dinasOptions' => $ctx['dinasOptions'] ?? collect(),
            'activeDinas' => $ctx['dinas'] ?? null,
            'activeDinasId' => (int) ($ctx['dinasId'] ?? 0),
            'isSuperAdmin' => (bool) ($ctx['isSuperAdmin'] ?? false),
        ]);
    }

    public function locations(Request $request)
    {
        $ctx = $this->resolveDinasContext($request);

        $locationsQuery = Location::query()->with('dinas')->orderBy('name');
        $dinasId = (int) ($ctx['dinasId'] ?? 0);

        if (!($ctx['isSuperAdmin'] ?? false)) {
            $locationsQuery->where('dinas_id', $dinasId);
        } elseif ($dinasId > 0) {
            $locationsQuery->where('dinas_id', $dinasId);
        }

        $locations = $locationsQuery->get();

        return view('admin.attendance.locations', [
            'locations' => $locations,
            'dinasOptions' => $ctx['dinasOptions'] ?? collect(),
            'activeDinas' => $ctx['dinas'] ?? null,
            'activeDinasId' => (int) ($ctx['dinasId'] ?? 0),
            'isSuperAdmin' => (bool) ($ctx['isSuperAdmin'] ?? false),
        ]);
    }

    public function storeLocation(Request $request)
    {
        $ctx = $this->resolveDinasContext($request, true);
        $dinasId = (int) ($ctx['dinasId'] ?? 0);

        if (($ctx['isSuperAdmin'] ?? false) && $dinasId <= 0) {
            return back()->withErrors(['dinas_id' => 'Pilih dinas terlebih dahulu.']);
        }

        $validated = $request->validate([
            'dinas_id' => ['nullable', 'integer', 'exists:dinas,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('locations', 'code')],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Location::query()->create([
            'dinas_id' => $dinasId,
            'name' => $validated['name'],
            'code' => ($validated['code'] ?? null) ?: null,
            'lat' => (float) $validated['lat'],
            'lng' => (float) $validated['lng'],
            'address' => ($validated['address'] ?? null) ?: null,
        ]);

        if (($request->user()?->role ?? null) === 'admin_dinas') {
            logger()->info('admin_dinas created location', [
                'actor_id' => $request->user()?->id,
                'dinas_id' => $dinasId,
                'location_name' => $validated['name'],
            ]);
        }

        return back()->with('status', 'Lokasi berhasil ditambahkan.');
    }

    public function updateLocation(Request $request, Location $location)
    {
        $user = $request->user();
        if (($user?->role ?? null) === 'admin_dinas' && (int) ($user?->dinas_id ?? 0) !== (int) ($location->dinas_id ?? 0)) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('locations', 'code')->ignore($location->id)],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $location->forceFill([
            'name' => $validated['name'],
            'code' => ($validated['code'] ?? null) ?: null,
            'lat' => (float) $validated['lat'],
            'lng' => (float) $validated['lng'],
            'address' => ($validated['address'] ?? null) ?: null,
        ])->save();

        if (($user?->role ?? null) === 'admin_dinas') {
            logger()->info('admin_dinas updated location', [
                'actor_id' => $user?->id,
                'dinas_id' => (int) ($user?->dinas_id ?? 0),
                'location_id' => $location->id,
            ]);
        }

        return back()->with('status', 'Lokasi berhasil diperbarui.');
    }

    public function destroyLocation(Request $request, Location $location)
    {
        $user = $request->user();
        if (($user?->role ?? null) === 'admin_dinas' && (int) ($user?->dinas_id ?? 0) !== (int) ($location->dinas_id ?? 0)) {
            abort(404);
        }

        if (($user?->role ?? null) === 'admin_dinas') {
            logger()->info('admin_dinas deleted location', [
                'actor_id' => $user?->id,
                'dinas_id' => (int) ($user?->dinas_id ?? 0),
                'location_id' => $location->id,
            ]);
        }

        $location->delete();

        return back()->with('status', 'Lokasi berhasil dihapus.');
    }

    public function toggleFakeGps(Request $request, Attendance $attendance)
    {
        if (($request->user()?->role ?? null) !== 'super_admin') {
            abort(403);
        }

        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if ($attendance->is_fake_gps) {
            $attendance->forceFill([
                'is_fake_gps' => false,
                'fake_gps_flagged_by' => null,
                'fake_gps_flagged_at' => null,
                'fake_gps_note' => null,
            ])->save();

            return back()->with('status', 'Flag Fake GPS dihapus.');
        }

        $attendance->forceFill([
            'is_fake_gps' => true,
            'fake_gps_flagged_by' => $request->user()?->id,
            'fake_gps_flagged_at' => now(),
            'fake_gps_note' => ($validated['note'] ?? null) ?: null,
        ])->save();

        return back()->with('status', 'Presensi ditandai sebagai Fake GPS.');
    }

    public function updateSettings(Request $request)
    {
        $ctx = $this->resolveDinasContext($request, true);
        $dinasId = (int) ($ctx['dinasId'] ?? 0);

        if (($ctx['isSuperAdmin'] ?? false) && $dinasId <= 0) {
            return back()->withErrors(['dinas_id' => 'Pilih dinas terlebih dahulu.']);
        }

        $validated = $request->validate([
            'dinas_id' => ['nullable', 'integer', 'exists:dinas,id'],
            'office_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'office_lng' => ['nullable', 'numeric', 'between:-180,180'],
            // Strict(maksimal radius).
            'radius_m' => ['required', 'integer', 'min:1', 'max:50'],
            'max_accuracy_m' => ['required', 'integer', 'min:1', 'max:5000'],
            'checkin_start' => ['required', 'date_format:H:i'],
            'checkin_end' => ['required', 'date_format:H:i'],
            'checkout_start' => ['required', 'date_format:H:i'],
            'checkout_end' => ['required', 'date_format:H:i'],
        ]);

        AttendanceRule::query()->updateOrCreate(
            ['dinas_id' => $dinasId],
            [
                'office_lat' => $validated['office_lat'] !== null ? (string) $validated['office_lat'] : null,
                'office_lng' => $validated['office_lng'] !== null ? (string) $validated['office_lng'] : null,
                'radius_m' => (int) $validated['radius_m'],
                'max_accuracy_m' => (int) $validated['max_accuracy_m'],
                'checkin_start' => (string) $validated['checkin_start'],
                'checkin_end' => (string) $validated['checkin_end'],
                'checkout_start' => (string) $validated['checkout_start'],
                'checkout_end' => (string) $validated['checkout_end'],
            ]
        );

        if (($request->user()?->role ?? null) === 'admin_dinas') {
            logger()->info('admin_dinas updated attendance rules', [
                'actor_id' => $request->user()?->id,
                'dinas_id' => $dinasId,
            ]);
        }

        return back()->with('status', 'Aturan presensi berhasil diperbarui.');
    }

    public function exportPdf(Request $request)
    {
        if (($request->user()?->role ?? null) !== 'super_admin') {
            abort(403);
        }

        $q = (string) $request->query('q', '');
        $dateFrom = (string) $request->query('date_from', '');
        $dateTo = (string) $request->query('date_to', '');
        $sort = (string) $request->query('sort', 'date');
        $dir = (string) $request->query('dir', 'desc');
        $dir = in_array($dir, ['asc', 'desc'], true) ? $dir : 'desc';

        $baseQuery = Attendance::query()
            ->select('attendances.*')
            ->with(['user', 'location']);

        if ($q !== '') {
            $baseQuery->whereHas('user', function ($userQuery) use ($q) {
                $userQuery->where('name', 'like', '%' . $q . '%');
            });
        }

        if ($dateFrom !== '') {
            $baseQuery->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $baseQuery->whereDate('date', '<=', $dateTo);
        }

        if ($sort === 'name') {
            $baseQuery
                ->join('users', 'users.id', '=', 'attendances.user_id')
                ->orderBy('users.name', $dir)
                ->orderByDesc('date')
                ->orderByDesc('check_in_at');
        } else {
            $baseQuery
                ->orderBy('date', $dir)
                ->orderByDesc('check_in_at');
        }

        $maxRows = 400;
        $attendances = $baseQuery->limit($maxRows)->get();

        $generatedAt = now();
        $filters = [
            'q' => $q,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'sort' => $sort,
            'dir' => $dir,
        ];

        $html = view('admin.attendance.export_pdf', [
            'generatedAt' => $generatedAt,
            'filters' => $filters,
            'attendances' => $attendances,
            'maxRows' => $maxRows,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'laporan-presensi-' . now()->format('Ymd-His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        if (($request->user()?->role ?? null) !== 'super_admin') {
            abort(403);
        }

        $q = (string) $request->query('q', '');
        $dateFrom = (string) $request->query('date_from', '');
        $dateTo = (string) $request->query('date_to', '');
        $sort = (string) $request->query('sort', 'date');
        $dir = (string) $request->query('dir', 'desc');
        $dir = in_array($dir, ['asc', 'desc'], true) ? $dir : 'desc';

        $baseQuery = Attendance::query()
            ->select('attendances.*')
            ->with(['user', 'location']);

        if ($q !== '') {
            $baseQuery->whereHas('user', function ($userQuery) use ($q) {
                $userQuery->where('name', 'like', '%' . $q . '%');
            });
        }

        if ($dateFrom !== '') {
            $baseQuery->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $baseQuery->whereDate('date', '<=', $dateTo);
        }

        if ($sort === 'name') {
            $baseQuery
                ->join('users', 'users.id', '=', 'attendances.user_id')
                ->orderBy('users.name', $dir)
                ->orderByDesc('date')
                ->orderByDesc('check_in_at');
        } else {
            $baseQuery
                ->orderBy('date', $dir)
                ->orderByDesc('check_in_at');
        }

        $maxRows = 5000;
        $attendances = $baseQuery->limit($maxRows)->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Sistem Absensi & Buku Tamu')
            ->setTitle('Laporan Presensi');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Presensi');
        $sheet->fromArray([
            ['Laporan Presensi'],
            ['Generated At', now()->format('Y-m-d H:i:s')],
            ['Filter q', $q],
            ['Filter date_from', $dateFrom],
            ['Filter date_to', $dateTo],
            [],
            ['Nama', 'Tanggal', 'Check-in', 'Check-out', 'Lokasi', 'Status', 'Catatan'],
        ]);

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A7:G7')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->freezePane('A8');

        $row = 8;
        foreach ($attendances as $a) {
            $status = '-';
            if (!empty($a->check_in_at) && empty($a->check_out_at)) {
                $status = 'Open';
            }
            if (!empty($a->check_in_at) && !empty($a->check_out_at)) {
                $status = 'Selesai';
            }

            $dateVal = '';
            if (!empty($a->date)) {
                $dateVal = ExcelDate::PHPToExcel($a->date->copy()->startOfDay());
            }
            $checkInVal = '';
            if (!empty($a->check_in_at)) {
                $checkInVal = ExcelDate::PHPToExcel($a->check_in_at);
            }
            $checkOutVal = '';
            if (!empty($a->check_out_at)) {
                $checkOutVal = ExcelDate::PHPToExcel($a->check_out_at);
            }

            $sheet->fromArray([
                (string) ($a->user?->name ?? ''),
                $dateVal,
                $checkInVal,
                $checkOutVal,
                (string) ($a->location?->name ?? ''),
                $status,
                (string) ($a->notes ?? ''),
            ], null, "A{$row}");
            $row++;
        }

        $lastRow = max(7, $row - 1);
        $sheet->setAutoFilter("A7:G{$lastRow}");
        if ($lastRow >= 8) {
            $sheet->getStyle("G8:G{$lastRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("B8:B{$lastRow}")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            $sheet->getStyle("C8:D{$lastRow}")->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'laporan-presensi-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
