<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\GuestSurvey;
use App\Models\GuestVisit;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
// use PhpOffice\PhpSpreadsheet\Style\Alignment;
// use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Style\Fill;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\Cell\DataType;


class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $intern_present_today = Attendance::whereDate('created_at', $today)
            ->whereNotNull('check_in_at')
            ->distinct('user_id')
            ->count('user_id');

        $intern_open_today = Attendance::whereDate('created_at', $today)
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->distinct('user_id')
            ->count('user_id');

        $stats = [
            'attendance_today' => $intern_present_today,
            'intern_open'      => $intern_open_today,
            'guest_today'      => GuestVisit::whereDate('created_at', $today)->count(),
            'survey_today'     => GuestSurvey::whereDate('created_at', $today)->count(),
            'users_total'      => User::count(),
        ];

        $chart = collect(range(6, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);

            return [
                'date'   => $date->format('d M'),
                'guest'  => GuestVisit::whereDate('created_at', $date)->count(),
                'survey' => GuestSurvey::whereDate('created_at', $date)->count(),
            ];
        });

        return view('dashboard', compact('stats', 'chart'));
    }

    public function exportPdf(Request $request)
    {
        $today = Carbon::today();

        $intern_present_today = Attendance::whereDate('created_at', $today)
            ->whereNotNull('check_in_at')
            ->distinct('user_id')
            ->count('user_id');

        $intern_open_today = Attendance::whereDate('created_at', $today)
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->distinct('user_id')
            ->count('user_id');

        $stats = [
            'attendance_today' => $intern_present_today,
            'intern_open'      => $intern_open_today,
            'guest_today'      => GuestVisit::whereDate('created_at', $today)->count(),
            'survey_today'     => GuestSurvey::whereDate('created_at', $today)->count(),
            'users_total'      => User::count(),
        ];

        $chart = collect(range(6, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);

            return [
                'date'   => $date->format('Y-m-d'),
                'guest'  => GuestVisit::whereDate('created_at', $date)->count(),
                'survey' => GuestSurvey::whereDate('created_at', $date)->count(),
            ];
        })->values();

        $generatedAt = now();

        $html = view('admin.dashboard.export_pdf', [
            'generatedAt' => $generatedAt,
            'stats'       => $stats,
            'chart'       => $chart,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'export-dashboard-' . now()->format('Ymd-His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // public function exportExcel(Request $request)
    // {
    //     $today = Carbon::today();

    //     // ======================
    //     // RINGKASAN (tetap ada)
    //     // ======================
    //     $internPresentToday = Attendance::whereDate('created_at', $today)
    //         ->whereNotNull('check_in_at')
    //         ->distinct('user_id')
    //         ->count('user_id');

    //     $internOpenToday = Attendance::whereDate('created_at', $today)
    //         ->whereNotNull('check_in_at')
    //         ->whereNull('check_out_at')
    //         ->distinct('user_id')
    //         ->count('user_id');

    //     $stats = [
    //         'attendance_today' => $internPresentToday,
    //         'intern_open'      => $internOpenToday,
    //         'guest_today'      => GuestVisit::whereDate('created_at', $today)->count(),
    //         'survey_today'     => GuestSurvey::whereDate('created_at', $today)->count(),
    //         'users_total'      => User::count(),
    //     ];

    //     // ======================
    //     // CHART 7 DAYS (tetap ada)
    //     // ======================
    //     $chart = collect(range(6, 0))->map(function ($i) {
    //         $date = Carbon::today()->subDays($i);

    //         return [
    //             'date'   => $date->format('Y-m-d'),
    //             'guest'  => GuestVisit::whereDate('created_at', $date)->count(),
    //             'survey' => GuestSurvey::whereDate('created_at', $date)->count(),
    //         ];
    //     })->values();

    //     // ======================
    //     // CREATE SPREADSHEET
    //     // ======================
    //     $spreadsheet = new Spreadsheet();
    //     $spreadsheet->getProperties()
    //         ->setCreator('Sistem Absensi & Buku Tamu')
    //         ->setTitle('Export Admin Full Data');

    //     // Helper style
    //     $headerStyle = [
    //         'font' => ['bold' => true],
    //         'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
    //         'alignment' => [
    //             'horizontal' => Alignment::HORIZONTAL_CENTER,
    //             'vertical'   => Alignment::VERTICAL_CENTER,
    //             'wrapText'   => true,
    //         ],
    //         'borders' => [
    //             'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']],
    //         ],
    //     ];

    //     $bodyStyle = [
    //         'borders' => [
    //             'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']],
    //         ],
    //         'alignment' => [
    //             'vertical' => Alignment::VERTICAL_CENTER,
    //             'wrapText' => true,
    //         ],
    //     ];

    //     // =========================================================
    //     // SHEET 1: RINGKASAN
    //     // =========================================================
    //     $sheet1 = $spreadsheet->getActiveSheet();
    //     $sheet1->setTitle('Ringkasan');

    //     $sheet1->fromArray([
    //         ['Export Admin - Full Data'],
    //         ['Generated At', now()->format('Y-m-d H:i:s')],
    //         [],
    //         ['Metric', 'Value'],
    //         ['Presensi hari ini', (int) ($stats['attendance_today'] ?? 0)],
    //         ['Intern masih open', (int) ($stats['intern_open'] ?? 0)],
    //         ['Buku tamu hari ini', (int) ($stats['guest_today'] ?? 0)],
    //         ['Survey hari ini', (int) ($stats['survey_today'] ?? 0)],
    //         ['Total users', (int) ($stats['users_total'] ?? 0)],
    //         [],
    //         ['Catatan', 'File ini berisi semua data (Users, Presensi, Buku Tamu, Survey).'],
    //     ]);

    //     $sheet1->mergeCells('A1:B1');
    //     $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14);

    //     $sheet1->getStyle('A4:B4')->applyFromArray($headerStyle);
    //     $sheet1->getStyle('A5:B9')->applyFromArray($bodyStyle);
    //     $sheet1->getStyle('B5:B9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    //     $sheet1->freezePane('A5');
    //     $sheet1->setAutoFilter('A4:B9');
    //     $sheet1->getColumnDimension('A')->setWidth(28);
    //     $sheet1->getColumnDimension('B')->setWidth(32);

    //     // =========================================================
    //     // SHEET 2: AKTIVITAS 7 HARI
    //     // =========================================================
    //     $sheet2 = $spreadsheet->createSheet();
    //     $sheet2->setTitle('Aktivitas 7 Hari');
    //     $sheet2->fromArray([['Tanggal', 'Tamu', 'Survey']]);

    //     $r = 2;
    //     foreach ($chart as $item) {
    //         $date = (string) ($item['date'] ?? '');
    //         $sheet2->setCellValue("A{$r}", $date ? ExcelDate::PHPToExcel(Carbon::parse($date)->startOfDay()) : '');
    //         $sheet2->setCellValue("B{$r}", (int) ($item['guest'] ?? 0));
    //         $sheet2->setCellValue("C{$r}", (int) ($item['survey'] ?? 0));
    //         $r++;
    //     }
    //     $lastRow2 = max(1, $r - 1);

    //     $sheet2->getStyle('A1:C1')->applyFromArray($headerStyle);
    //     if ($lastRow2 >= 2) {
    //         $sheet2->getStyle("A2:A{$lastRow2}")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
    //         $sheet2->getStyle("A2:C{$lastRow2}")->applyFromArray($bodyStyle);
    //         $sheet2->getStyle("B2:C{$lastRow2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    //     }
    //     $sheet2->freezePane('A2');
    //     $sheet2->setAutoFilter("A1:C{$lastRow2}");
    //     $sheet2->getColumnDimension('A')->setWidth(14);
    //     $sheet2->getColumnDimension('B')->setWidth(10);
    //     $sheet2->getColumnDimension('C')->setWidth(10);

    //     // =========================================================
    //     // SHEET 3: USERS (SEMUA)
    //     // =========================================================
    //     $sheet3 = $spreadsheet->createSheet();
    //     $sheet3->setTitle('Users');

    //     $sheet3->fromArray([[
    //         'ID', 'Nama', 'Username', 'Email', 'Phone', 'NIK', 'Role',
    //         'Start Magang', 'End Magang', 'Lokasi Magang', 'Created At'
    //     ]]);

    //     $row = 2;
    //     User::with(['internshipLocation'])
    //         ->orderBy('id')
    //         ->chunk(500, function ($users) use ($sheet3, &$row) {
    //             foreach ($users as $u) {
    //                 // NIK sering leading zero -> pakai string
    //                 $sheet3->setCellValue("A{$row}", $u->id);
    //                 $sheet3->setCellValue("B{$row}", $u->name ?? '');
    //                 $sheet3->setCellValue("C{$row}", $u->username ?? '');
    //                 $sheet3->setCellValue("D{$row}", $u->email ?? '');
    //                 $sheet3->setCellValue("E{$row}", $u->phone ?? '');
    //                 $sheet3->setCellValueExplicit("F{$row}", (string) ($u->nik ?? ''), DataType::TYPE_STRING);
    //                 $sheet3->setCellValue("G{$row}", $u->role ?? '');

    //                 // tanggal magang (string aman)
    //                 $sheet3->setCellValue("H{$row}", $u->internship_start_date ? Carbon::parse($u->internship_start_date)->format('Y-m-d') : '');
    //                 $sheet3->setCellValue("I{$row}", $u->internship_end_date ? Carbon::parse($u->internship_end_date)->format('Y-m-d') : '');

    //                 $locName = $u->internshipLocation?->name ?? '';
    //                 $locCode = $u->internshipLocation?->code ?? '';
    //                 $sheet3->setCellValue("J{$row}", trim($locName . ($locCode ? " ({$locCode})" : '')));

    //                 $sheet3->setCellValue("K{$row}", $u->created_at ? $u->created_at->format('Y-m-d H:i:s') : '');
    //                 $row++;
    //             }
    //         });

    //     $lastRow3 = max(1, $row - 1);
    //     $sheet3->getStyle("A1:K1")->applyFromArray($headerStyle);
    //     if ($lastRow3 >= 2) $sheet3->getStyle("A2:K{$lastRow3}")->applyFromArray($bodyStyle);

    //     $sheet3->freezePane('A2');
    //     $sheet3->setAutoFilter("A1:K{$lastRow3}");
    //     foreach (range('A', 'K') as $col) $sheet3->getColumnDimension($col)->setWidth(18);
    //     $sheet3->getColumnDimension('B')->setWidth(24);
    //     $sheet3->getColumnDimension('D')->setWidth(26);
    //     $sheet3->getColumnDimension('J')->setWidth(26);

    //     // =========================================================
    //     // SHEET 4: PRESENSI (SEMUA)
    //     // =========================================================
    //     $sheet4 = $spreadsheet->createSheet();
    //     $sheet4->setTitle('Presensi');

    //     $sheet4->fromArray([[
    //         'ID', 'User ID', 'Nama', 'Tanggal', 'Check-in', 'Check-out',
    //         'Status', 'Created At'
    //     ]]);

    //     $row = 2;
    //     Attendance::with(['user'])
    //         ->orderBy('id')
    //         ->chunk(1000, function ($items) use ($sheet4, &$row) {
    //             foreach ($items as $a) {
    //                 $sheet4->setCellValue("A{$row}", $a->id);
    //                 $sheet4->setCellValue("B{$row}", $a->user_id);
    //                 $sheet4->setCellValue("C{$row}", $a->user?->name ?? '');

    //                 // Sesuaikan jika kamu punya kolom "date" sendiri
    //                 $date = $a->created_at ? $a->created_at->format('Y-m-d') : '';
    //                 $sheet4->setCellValue("D{$row}", $date);

    //                 $sheet4->setCellValue("E{$row}", $a->check_in_at ? Carbon::parse($a->check_in_at)->format('Y-m-d H:i:s') : '');
    //                 $sheet4->setCellValue("F{$row}", $a->check_out_at ? Carbon::parse($a->check_out_at)->format('Y-m-d H:i:s') : '');

    //                 // status sederhana
    //                 $status = ($a->check_in_at && $a->check_out_at) ? 'DONE' : (($a->check_in_at && !$a->check_out_at) ? 'OPEN' : '—');
    //                 $sheet4->setCellValue("G{$row}", $status);

    //                 $sheet4->setCellValue("H{$row}", $a->created_at ? $a->created_at->format('Y-m-d H:i:s') : '');
    //                 $row++;
    //             }
    //         });

    //     $lastRow4 = max(1, $row - 1);
    //     $sheet4->getStyle("A1:H1")->applyFromArray($headerStyle);
    //     if ($lastRow4 >= 2) $sheet4->getStyle("A2:H{$lastRow4}")->applyFromArray($bodyStyle);
    //     $sheet4->freezePane('A2');
    //     $sheet4->setAutoFilter("A1:H{$lastRow4}");
    //     foreach (range('A', 'H') as $col) $sheet4->getColumnDimension($col)->setWidth(18);
    //     $sheet4->getColumnDimension('C')->setWidth(24);

    //     // =========================================================
    //     // SHEET 5: BUKU TAMU (SEMUA)
    //     // =========================================================
    //     $sheet5 = $spreadsheet->createSheet();
    //     $sheet5->setTitle('Buku Tamu');

    //     $sheet5->fromArray([[
    //         'ID', 'Nama', 'Instansi', 'Keperluan', 'Status',
    //         'Arrived At', 'Done At', 'Created At'
    //     ]]);

    //     $row = 2;
    //     GuestVisit::orderBy('id')
    //         ->chunk(1000, function ($items) use ($sheet5, &$row) {
    //             foreach ($items as $g) {
    //                 $sheet5->setCellValue("A{$row}", $g->id);
    //                 $sheet5->setCellValue("B{$row}", $g->name ?? '');
    //                 $sheet5->setCellValue("C{$row}", $g->institution ?? '');
    //                 $sheet5->setCellValue("D{$row}", $g->purpose ?? '');

    //                 // sesuaikan nama kolom status milikmu (mis: status / is_done / etc)
    //                 $sheet5->setCellValue("E{$row}", $g->status ?? '');

    //                 // sesuaikan nama kolom waktu milikmu
    //                 $sheet5->setCellValue("F{$row}", $g->arrived_at ? Carbon::parse($g->arrived_at)->format('Y-m-d H:i:s') : '');
    //                 $sheet5->setCellValue("G{$row}", $g->done_at ? Carbon::parse($g->done_at)->format('Y-m-d H:i:s') : '');

    //                 $sheet5->setCellValue("H{$row}", $g->created_at ? $g->created_at->format('Y-m-d H:i:s') : '');
    //                 $row++;
    //             }
    //         });

    //     $lastRow5 = max(1, $row - 1);
    //     $sheet5->getStyle("A1:H1")->applyFromArray($headerStyle);
    //     if ($lastRow5 >= 2) $sheet5->getStyle("A2:H{$lastRow5}")->applyFromArray($bodyStyle);
    //     $sheet5->freezePane('A2');
    //     $sheet5->setAutoFilter("A1:H{$lastRow5}");
    //     foreach (range('A', 'H') as $col) $sheet5->getColumnDimension($col)->setWidth(18);
    //     $sheet5->getColumnDimension('B')->setWidth(24);
    //     $sheet5->getColumnDimension('D')->setWidth(34);

    //     // =========================================================
    //     // SHEET 6: SURVEY (SEMUA)
    //     // =========================================================
    //     $sheet6 = $spreadsheet->createSheet();
    //     $sheet6->setTitle('Survey');

    //     $sheet6->fromArray([[
    //         'ID', 'Visit ID', 'Nama (jika ada)', 'Rating', 'Komentar', 'Created At'
    //     ]]);

    //     $row = 2;
    //     GuestSurvey::with(['visit']) // jika relasi "visit" ada
    //         ->orderBy('id')
    //         ->chunk(1000, function ($items) use ($sheet6, &$row) {
    //             foreach ($items as $s) {
    //                 $sheet6->setCellValue("A{$row}", $s->id);

    //                 // sesuaikan nama foreign key
    //                 $visitId = $s->guest_visit_id ?? $s->visit_id ?? null;
    //                 $sheet6->setCellValue("B{$row}", $visitId);

    //                 // jika survey nyimpan nama / ambil dari visit
    //                 $name = $s->name ?? $s->visit?->name ?? '';
    //                 $sheet6->setCellValue("C{$row}", $name);

    //                 // sesuaikan field rating/score
    //                 $sheet6->setCellValue("D{$row}", $s->rating ?? $s->score ?? '');

    //                 // sesuaikan field komentar
    //                 $sheet6->setCellValue("E{$row}", $s->comment ?? $s->feedback ?? '');

    //                 $sheet6->setCellValue("F{$row}", $s->created_at ? $s->created_at->format('Y-m-d H:i:s') : '');
    //                 $row++;
    //             }
    //         });

    //     $lastRow6 = max(1, $row - 1);
    //     $sheet6->getStyle("A1:F1")->applyFromArray($headerStyle);
    //     if ($lastRow6 >= 2) $sheet6->getStyle("A2:F{$lastRow6}")->applyFromArray($bodyStyle);
    //     $sheet6->freezePane('A2');
    //     $sheet6->setAutoFilter("A1:F{$lastRow6}");
    //     foreach (range('A', 'F') as $col) $sheet6->getColumnDimension($col)->setWidth(18);
    //     $sheet6->getColumnDimension('C')->setWidth(24);
    //     $sheet6->getColumnDimension('E')->setWidth(48);

    //     // ======================
    //     // OUTPUT
    //     // ======================
    //     $filename = 'export-admin-full-' . now()->format('Ymd-His') . '.xlsx';

    //     return response()->streamDownload(function () use ($spreadsheet) {
    //         $writer = new Xlsx($spreadsheet);
    //         $writer->save('php://output');
    //     }, $filename, [
    //         'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //     ]);
    // }
}
