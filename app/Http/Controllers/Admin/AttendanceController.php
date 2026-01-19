<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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
    public function index(Request $request)
    {
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

        $settings = [
            'office_lat' => AppSettings::getString(AppSettings::OFFICE_LAT, ''),
            'office_lng' => AppSettings::getString(AppSettings::OFFICE_LNG, ''),
            'radius_m' => AppSettings::getInt(AppSettings::RADIUS_M, 50),
            'max_accuracy_m' => AppSettings::getInt(AppSettings::MAX_ACCURACY_M, 50),
            'checkin_start' => AppSettings::getString(AppSettings::CHECKIN_START, '08:00'),
            'checkin_end' => AppSettings::getString(AppSettings::CHECKIN_END, '12:00'),
            'checkout_start' => AppSettings::getString(AppSettings::CHECKOUT_START, '13:00'),
            'checkout_end' => AppSettings::getString(AppSettings::CHECKOUT_END, '16:30'),
        ];

        return view('admin.attendance.index', [
            'attendances' => $attendances,
            'filters' => [
                'q' => $q,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort' => $sort,
                'dir' => $dir,
            ],
            'settings' => $settings,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'office_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'office_lng' => ['nullable', 'numeric', 'between:-180,180'],
            // Strict: radius maksimum 50m.
            'radius_m' => ['required', 'integer', 'min:1', 'max:50'],
            'max_accuracy_m' => ['required', 'integer', 'min:1', 'max:5000'],
            'checkin_start' => ['required', 'date_format:H:i'],
            'checkin_end' => ['required', 'date_format:H:i'],
            'checkout_start' => ['required', 'date_format:H:i'],
            'checkout_end' => ['required', 'date_format:H:i'],
        ]);

        Setting::setValue(AppSettings::OFFICE_LAT, $validated['office_lat'] !== null ? (string) $validated['office_lat'] : '');
        Setting::setValue(AppSettings::OFFICE_LNG, $validated['office_lng'] !== null ? (string) $validated['office_lng'] : '');
        Setting::setValue(AppSettings::RADIUS_M, (string) $validated['radius_m']);
        Setting::setValue(AppSettings::MAX_ACCURACY_M, (string) $validated['max_accuracy_m']);
        Setting::setValue(AppSettings::CHECKIN_START, (string) $validated['checkin_start']);
        Setting::setValue(AppSettings::CHECKIN_END, (string) $validated['checkin_end']);
        Setting::setValue(AppSettings::CHECKOUT_START, (string) $validated['checkout_start']);
        Setting::setValue(AppSettings::CHECKOUT_END, (string) $validated['checkout_end']);

        return back()->with('status', 'Aturan presensi berhasil diperbarui.');
    }

    public function exportPdf(Request $request)
    {
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
