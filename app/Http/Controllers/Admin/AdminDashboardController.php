<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\GuestVisit;
use App\Models\GuestSurvey;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            'intern_open' => $intern_open_today,
            'guest_today' => GuestVisit::whereDate('created_at', $today)->count(),
            'survey_today' => GuestSurvey::whereDate('created_at', $today)->count(),
            'users_total' => User::count(),
        ];

        $chart = collect(range(6, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);

            return [
                'date' => $date->format('Y-m-d'),
                'guest' => GuestVisit::whereDate('created_at', $date)->count(),
                'survey' => GuestSurvey::whereDate('created_at', $date)->count(),
            ];
        });

        $generatedAt = now();
        $html = view('admin.dashboard.export_pdf', [
            'generatedAt' => $generatedAt,
            'stats' => $stats,
            'chart' => $chart,
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
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportExcel(Request $request)
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
            'intern_open' => $intern_open_today,
            'guest_today' => GuestVisit::whereDate('created_at', $today)->count(),
            'survey_today' => GuestSurvey::whereDate('created_at', $today)->count(),
            'users_total' => User::count(),
        ];

        $chart = collect(range(6, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);

            return [
                'date' => $date->format('Y-m-d'),
                'guest' => GuestVisit::whereDate('created_at', $date)->count(),
                'survey' => GuestSurvey::whereDate('created_at', $date)->count(),
            ];
        })->values();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Sistem Absensi & Buku Tamu')
            ->setTitle('Export Dashboard');

        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Ringkasan');
        $sheet1->fromArray([
            ['Export Dashboard'],
            ['Generated At', now()->format('Y-m-d H:i:s')],
            [],
            ['Metric', 'Value'],
            ['Presensi hari ini', (int) ($stats['attendance_today'] ?? 0)],
            ['Intern masih open', (int) ($stats['intern_open'] ?? 0)],
            ['Buku tamu hari ini', (int) ($stats['guest_today'] ?? 0)],
            ['Survey hari ini', (int) ($stats['survey_today'] ?? 0)],
            ['Total users', (int) ($stats['users_total'] ?? 0)],
        ]);

        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet1->getStyle('A4:B4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet1->freezePane('A5');
        $sheet1->setAutoFilter('A4:B9');
        $sheet1->getColumnDimension('A')->setWidth(28);
        $sheet1->getColumnDimension('B')->setWidth(22);

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Aktivitas 7 Hari');
        $sheet2->fromArray([
            ['Tanggal', 'Tamu', 'Survey'],
        ]);
        $row = 2;
        foreach ($chart as $item) {
            $date = (string) ($item['date'] ?? '');
            if ($date !== '') {
                $sheet2->setCellValue("A{$row}", ExcelDate::PHPToExcel(Carbon::parse($date)->startOfDay()));
            } else {
                $sheet2->setCellValue("A{$row}", '');
            }
            $sheet2->setCellValue("B{$row}", (int) ($item['guest'] ?? 0));
            $sheet2->setCellValue("C{$row}", (int) ($item['survey'] ?? 0));
            $row++;
        }

        $lastRow = max(1, $row - 1);
        if ($lastRow >= 2) {
            $sheet2->getStyle("A2:A{$lastRow}")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        }
        $sheet2->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet2->freezePane('A2');
        $sheet2->setAutoFilter("A1:C{$lastRow}");
        $sheet2->getColumnDimension('A')->setWidth(14);
        $sheet2->getColumnDimension('B')->setWidth(10);
        $sheet2->getColumnDimension('C')->setWidth(10);

        $filename = 'export-dashboard-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
