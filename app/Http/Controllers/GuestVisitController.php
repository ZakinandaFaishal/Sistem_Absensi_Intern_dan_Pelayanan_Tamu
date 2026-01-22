<?php

namespace App\Http\Controllers;

use App\Models\GuestVisit;
use Carbon\CarbonImmutable;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GuestVisitController extends Controller
{
    public function create()
    {
        return view('guest.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'email' => ['nullable', 'email', 'max:150'],
            'education' => ['nullable', 'string', 'max:30'],
            'institution' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'job' => ['nullable', 'string', 'max:120'],
            'jabatan' => ['nullable', 'string', 'max:120'],
            'service_type' => ['required', 'string', Rule::in(['layanan', 'koordinasi', 'berkas', 'lainnya'])],
            'purpose_detail' => ['required', 'string', 'max:500'],
            'visit_type' => ['required', Rule::in(['single','group'])],
            'group_count' => ['nullable','integer','min:2','max:50','required_if:visit_type,group'],
            'group_names' => ['nullable','array','required_if:visit_type,group'],
            'group_names.*' => ['nullable','string','max:100','required_if:visit_type,group'],
        ]);

        $purpose = '[' . $validated['service_type'] . '] ' . trim($validated['purpose_detail']);
        $extras = [];
        if (!empty($validated['job'])) {
            $extras[] = 'Pekerjaan: ' . trim($validated['job']);
        }
        if (!empty($validated['jabatan'])) {
            $extras[] = 'Jabatan: ' . trim($validated['jabatan']);
        }
        if ($extras !== []) {
            $purpose .= ' (' . implode(', ', $extras) . ')';
        }

        $purpose = Str::of($purpose)->limit(255, '')->toString();

        $visit = GuestVisit::query()->create([
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'email' => $validated['email'],
            'education' => $validated['education'] ?? null,
            'institution' => $validated['institution'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'job' => $validated['job'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'service_type' => $validated['service_type'],
            'purpose_detail' => $validated['purpose_detail'],
            'purpose' => $purpose,
            'arrived_at' => CarbonImmutable::now(),
            'visit_type' => $validated['visit_type'],
            'group_count' => $validated['visit_type'] === 'group' ? $validated['group_count'] : null,
            'group_names' => $validated['visit_type'] === 'group' ? array_values($validated['group_names'] ?? []) : null,
        ]);

        return view('guest.thanks', [
            'visit' => $visit,
        ]);
    }

    public function index(Request $request)
    {
        $q         = trim((string) $request->query('q', ''));
        $status    = (string) $request->query('status', '');
        $visitType = (string) $request->query('visit_type', '');
        $from      = (string) $request->query('from', '');
        $to        = (string) $request->query('to', '');
        $sort      = (string) $request->query('sort', 'arrived_at');
        $dir       = strtolower((string) $request->query('dir', 'desc'));

        // ✅ allow sort visit_type
        $allowedSort = ['arrived_at', 'completed_at', 'name', 'visit_type'];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'arrived_at';
        }
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        $visitsQuery = GuestVisit::query();

        if ($q !== '') {
            $visitsQuery->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                ->orWhere('purpose', 'like', "%{$q}%")
                ->orWhere('institution', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($status === 'pending') {
            $visitsQuery->whereNull('completed_at');
        } elseif ($status === 'done') {
            $visitsQuery->whereNotNull('completed_at');
        }

        // ✅ Filter kelompok / sendiri
        if (in_array($visitType, ['single', 'group'], true)) {
            $visitsQuery->where('visit_type', $visitType);
        }

        if ($from !== '') {
            $visitsQuery->whereDate('arrived_at', '>=', $from);
        }
        if ($to !== '') {
            $visitsQuery->whereDate('arrived_at', '<=', $to);
        }

        // ✅ Sorting
        if ($sort === 'visit_type') {
            $visitsQuery->orderByRaw("CASE WHEN visit_type='single' THEN 0 WHEN visit_type='group' THEN 1 ELSE 2 END {$dir}");
            $visitsQuery->orderByDesc('arrived_at');
        } else {
            $visitsQuery->orderBy($sort, $dir);
        }

        $visits = $visitsQuery
            ->paginate(20)
            ->withQueryString();

        return view('admin.guest.index', [
            'visits' => $visits,
        ]);
    }



    public function complete(Request $request, GuestVisit $visit)
    {
        if ($visit->completed_at !== null) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['ok' => true, 'status' => 'already_completed']);
            }

            return redirect()
                ->route('admin.guest.index')
                ->with('status', 'Kunjungan sudah ditandai selesai.');
        }

        $visit->fill([
            'completed_at' => CarbonImmutable::now(),
            'handled_by' => $request->user()->id,
        ])->save();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'status' => 'completed']);
        }

        return redirect()
            ->route('admin.guest.index')
            ->with('status', 'Kunjungan berhasil ditandai selesai.');
    }

    public function exportPdf(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');
        $sort = (string) $request->query('sort', 'arrived_at');
        $dir = strtolower((string) $request->query('dir', 'desc'));

        $allowedSort = ['arrived_at', 'completed_at', 'name'];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'arrived_at';
        }
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        $visitsQuery = GuestVisit::query()->with(['handler'])->withExists('survey');

        if ($q !== '') {
            $visitsQuery->where(function ($qb) use ($q) {
                $qb
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('purpose', 'like', "%{$q}%")
                    ->orWhere('institution', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($status === 'pending') {
            $visitsQuery->whereNull('completed_at');
        } elseif ($status === 'done') {
            $visitsQuery->whereNotNull('completed_at');
        }

        if ($from !== '') {
            $visitsQuery->whereDate('arrived_at', '>=', $from);
        }
        if ($to !== '') {
            $visitsQuery->whereDate('arrived_at', '<=', $to);
        }

        $maxRows = 400;
        $visits = $visitsQuery
            ->orderBy($sort, $dir)
            ->limit($maxRows)
            ->get();

        $filters = [
            'q' => $q,
            'status' => $status,
            'from' => $from,
            'to' => $to,
            'sort' => $sort,
            'dir' => $dir,
        ];

        $generatedAt = now();
        $html = view('admin.guest.export_pdf', [
            'generatedAt' => $generatedAt,
            'filters' => $filters,
            'visits' => $visits,
            'maxRows' => $maxRows,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'laporan-buku-tamu-' . now()->format('Ymd-His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');
        $sort = (string) $request->query('sort', 'arrived_at');
        $dir = strtolower((string) $request->query('dir', 'desc'));

        $allowedSort = ['arrived_at', 'completed_at', 'name'];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'arrived_at';
        }
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        $visitsQuery = GuestVisit::query()->with(['handler'])->withExists('survey');

        if ($q !== '') {
            $visitsQuery->where(function ($qb) use ($q) {
                $qb
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('purpose', 'like', "%{$q}%")
                    ->orWhere('institution', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($status === 'pending') {
            $visitsQuery->whereNull('completed_at');
        } elseif ($status === 'done') {
            $visitsQuery->whereNotNull('completed_at');
        }

        if ($from !== '') {
            $visitsQuery->whereDate('arrived_at', '>=', $from);
        }
        if ($to !== '') {
            $visitsQuery->whereDate('arrived_at', '<=', $to);
        }

        $maxRows = 5000;
        $visits = $visitsQuery
            ->orderBy($sort, $dir)
            ->limit($maxRows)
            ->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Sistem Absensi & Buku Tamu')
            ->setTitle('Laporan Buku Tamu');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Buku Tamu');
        $sheet->fromArray([
            ['Laporan Buku Tamu'],
            ['Generated At', now()->format('Y-m-d H:i:s')],
            ['Filter q', $q],
            ['Filter status', $status],
            ['Filter from', $from],
            ['Filter to', $to],
            [],
            ['Arrived At', 'Nama', 'Email', 'Instansi', 'Layanan', 'Keperluan', 'Status', 'Survey', 'Petugas', 'Completed At'],
        ]);

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A8:J8')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->freezePane('A9');

        $row = 9;
        foreach ($visits as $v) {
            $done = $v->completed_at !== null;

            $arrivedVal = '';
            if (!empty($v->arrived_at)) {
                $arrivedVal = ExcelDate::PHPToExcel($v->arrived_at);
            }
            $completedVal = '';
            if (!empty($v->completed_at)) {
                $completedVal = ExcelDate::PHPToExcel($v->completed_at);
            }

            $sheet->fromArray([
                $arrivedVal,
                (string) $v->name,
                (string) $v->email,
                (string) ($v->institution ?? ''),
                (string) ($v->service_type ?? ''),
                (string) ($v->purpose ?? ''),
                $done ? 'Selesai' : 'Pending',
                ((bool) ($v->survey_exists ?? false)) ? 'Sudah' : 'Belum',
                (string) ($v->handler?->name ?? ''),
                $completedVal,
            ], null, "A{$row}");
            $row++;
        }

        $lastRow = max(8, $row - 1);
        $sheet->setAutoFilter("A8:J{$lastRow}");
        if ($lastRow >= 9) {
            $sheet->getStyle("F9:F{$lastRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("A9:A{$lastRow}")->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
            $sheet->getStyle("J9:J{$lastRow}")->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'laporan-buku-tamu-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function active()
    {
        $visits = GuestVisit::query()
            ->whereNull('completed_at')
            ->withExists('survey')
            ->orderByDesc('arrived_at')
            ->limit(20)
            ->get([
                'id',
                'name',
                'purpose',
                'arrived_at',
                'service_type',
            ]);

        return response()->json([
            'data' => $visits->map(function ($v) {
                $isLayanan = $v->service_type === 'layanan';

                return [
                    'id' => $v->id,
                    'name' => $v->name,
                    'purpose' => $v->purpose,
                    'arrived_at' => optional($v->arrived_at)->format('d M Y H:i'),
                    'status' => 'Sedang berkunjung',

                    'service_type'   => $v->service_type,
                    'survey_allowed' => $isLayanan,

                    'survey_filled'  => $isLayanan
                        ? (bool) ($v->survey_exists ?? false)
                        : true,

                    'survey_url' => $isLayanan
                        ? route('guest.survey.show', $v)
                        : null,
                ];
            })->values(),
        ]);
    }
    private function applyFiltersAndSort(GuestVisit|\Illuminate\Database\Eloquent\Builder $query, Request $request): array
    {
        $q         = trim((string) $request->query('q', ''));
        $status    = (string) $request->query('status', '');
        $visitType = (string) $request->query('visit_type', ''); // ✅ NEW
        $from      = (string) $request->query('from', '');
        $to        = (string) $request->query('to', '');
        $sort      = (string) $request->query('sort', 'arrived_at');
        $dir       = strtolower((string) $request->query('dir', 'desc'));

        // ✅ allow sort visit_type
        $allowedSort = ['arrived_at', 'completed_at', 'name', 'visit_type'];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'arrived_at';
        }
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        if ($q !== '') {
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                ->orWhere('purpose', 'like', "%{$q}%")
                ->orWhere('institution', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($status === 'pending') {
            $query->whereNull('completed_at');
        } elseif ($status === 'done') {
            $query->whereNotNull('completed_at');
        }

        // ✅ NEW: filter single / group
        if (in_array($visitType, ['single', 'group'], true)) {
            $query->where('visit_type', $visitType);
        }

        if ($from !== '') {
            $query->whereDate('arrived_at', '>=', $from);
        }
        if ($to !== '') {
            $query->whereDate('arrived_at', '<=', $to);
        }

        if ($sort === 'visit_type') {
            $query->orderByRaw("CASE WHEN visit_type = 'single' THEN 0 WHEN visit_type = 'group' THEN 1 ELSE 2 END {$dir}");
            $query->orderByDesc('arrived_at');
        } else {
            $query->orderBy($sort, $dir);
        }

        return compact('q', 'status', 'visitType', 'from', 'to', 'sort', 'dir');
    }

}
