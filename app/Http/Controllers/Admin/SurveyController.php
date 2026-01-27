<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestSurvey;
use App\Support\Ikm;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SurveyController extends Controller
{
    private function scopeByDinas(Request $request, $builder): void
    {
        $actor = $request->user();
        $role = (string) ($actor?->role ?? '');

        if ($role === 'admin_dinas') {
            $actorDinasId = (int) ($actor->dinas_id ?? 0);
            if ($actorDinasId > 0) {
                $builder->whereHas('visit', fn($v) => $v->where('dinas_id', $actorDinasId));
            } else {
                $builder->whereRaw('1=0');
            }
            return;
        }

        if ($role === 'super_admin') {
            $dinasId = (int) $request->query('dinas_id', 0);
            if ($dinasId > 0) {
                $builder->whereHas('visit', fn($v) => $v->where('dinas_id', $dinasId));
            }
            return;
        }

        abort(403);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $q = trim((string) $request->query('q', ''));
        $avgMin = (string) $request->query('avg_min', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');
        $sort = (string) $request->query('sort', 'submitted_at');
        $dir = strtolower((string) $request->query('dir', 'desc'));

        $dir = $dir === 'asc' ? 'asc' : 'desc';
        $allowedSort = ['submitted_at', 'avg', 'name'];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'submitted_at';
        }

        $avgExpr = '((guest_surveys.q1 + guest_surveys.q2 + guest_surveys.q3 + guest_surveys.q4 + guest_surveys.q5 + guest_surveys.q6 + guest_surveys.q7 + guest_surveys.q8 + guest_surveys.q9) / 9.0)';

        $applyFilters = function ($builder) use ($q, $avgMin, $from, $to, $avgExpr) {
            if ($q !== '') {
                $builder->where(function ($qb) use ($q) {
                    $qb
                        ->where('comment', 'like', "%{$q}%")
                        ->orWhereHas('visit', function ($v) use ($q) {
                            $v
                                ->where('name', 'like', "%{$q}%")
                                ->orWhere('purpose', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                });
            }

            if ($from !== '') {
                $builder->whereDate('submitted_at', '>=', $from);
            }
            if ($to !== '') {
                $builder->whereDate('submitted_at', '<=', $to);
            }

            if ($avgMin !== '') {
                $min = (float) $avgMin;
                $builder
                    ->whereNotNull('q1')
                    ->whereNotNull('q2')
                    ->whereNotNull('q3')
                    ->whereNotNull('q4')
                    ->whereNotNull('q5')
                    ->whereNotNull('q6')
                    ->whereNotNull('q7')
                    ->whereNotNull('q8')
                    ->whereNotNull('q9')
                    ->whereRaw("{$avgExpr} >= ?", [$min]);
            }
        };

        // Summary (IKM) based on all filtered rows (not just current page).
        $summaryQuery = GuestSurvey::query()->with('visit');

        // admin_dinas hanya melihat survey dari buku tamu dinasnya.
        if (($user?->role ?? null) === 'admin_dinas') {
            $dinasId = (int) ($user->dinas_id ?? 0);
            if ($dinasId > 0) {
                $summaryQuery->whereHas('visit', fn($v) => $v->where('dinas_id', $dinasId));
            } else {
                $summaryQuery->whereRaw('1=0');
            }
        }
        $applyFilters($summaryQuery);
        $summaryQuery
            ->whereNotNull('q1')
            ->whereNotNull('q2')
            ->whereNotNull('q3')
            ->whereNotNull('q4')
            ->whereNotNull('q5')
            ->whereNotNull('q6')
            ->whereNotNull('q7')
            ->whereNotNull('q8')
            ->whereNotNull('q9');

        $agg = (clone $summaryQuery)
            ->selectRaw('COUNT(*) as n')
            ->selectRaw('AVG(q1) as avg_q1')
            ->selectRaw('AVG(q2) as avg_q2')
            ->selectRaw('AVG(q3) as avg_q3')
            ->selectRaw('AVG(q4) as avg_q4')
            ->selectRaw('AVG(q5) as avg_q5')
            ->selectRaw('AVG(q6) as avg_q6')
            ->selectRaw('AVG(q7) as avg_q7')
            ->selectRaw('AVG(q8) as avg_q8')
            ->selectRaw('AVG(q9) as avg_q9')
            ->first();

        $avgByKey = [
            'q1' => $agg?->avg_q1 !== null ? (float) $agg->avg_q1 : null,
            'q2' => $agg?->avg_q2 !== null ? (float) $agg->avg_q2 : null,
            'q3' => $agg?->avg_q3 !== null ? (float) $agg->avg_q3 : null,
            'q4' => $agg?->avg_q4 !== null ? (float) $agg->avg_q4 : null,
            'q5' => $agg?->avg_q5 !== null ? (float) $agg->avg_q5 : null,
            'q6' => $agg?->avg_q6 !== null ? (float) $agg->avg_q6 : null,
            'q7' => $agg?->avg_q7 !== null ? (float) $agg->avg_q7 : null,
            'q8' => $agg?->avg_q8 !== null ? (float) $agg->avg_q8 : null,
            'q9' => $agg?->avg_q9 !== null ? (float) $agg->avg_q9 : null,
        ];

        $overall = Ikm::fromAverages($avgByKey);

        $ikmSummary = [
            'n' => (int) ($agg?->n ?? 0),
            'avg_by_key' => $avgByKey,
            'overall_nrr' => (float) $overall['nrr'],
            'overall_ikm' => (float) $overall['ikm'],
            'mutu' => (string) $overall['mutu'],
            'kinerja' => (string) $overall['kinerja'],
        ];

        $surveysQuery = GuestSurvey::query()->with(['visit']);

        if (($user?->role ?? null) === 'admin_dinas') {
            $dinasId = (int) ($user->dinas_id ?? 0);
            if ($dinasId > 0) {
                $surveysQuery->whereHas('visit', fn($v) => $v->where('dinas_id', $dinasId));
            } else {
                $surveysQuery->whereRaw('1=0');
            }
        }
        $applyFilters($surveysQuery);

        if ($sort === 'avg') {
            $surveysQuery->orderByRaw("{$avgExpr} {$dir}");
        } elseif ($sort === 'name') {
            $surveysQuery
                ->leftJoin('guest_visits as gv', 'gv.id', '=', 'guest_surveys.visit_id')
                ->select('guest_surveys.*')
                ->orderBy('gv.name', $dir);
        } else {
            $surveysQuery->orderBy('submitted_at', $dir);
        }

        $surveys = $surveysQuery
            ->paginate(20)
            ->withQueryString();

        return view('admin.survey.list', [
            'surveys' => $surveys,
        ]);
    }

    public function ikm(Request $request)
    {
        $user = $request->user();

        $q = trim((string) $request->query('q', ''));
        $avgMin = (string) $request->query('avg_min', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $avgExpr = '((guest_surveys.q1 + guest_surveys.q2 + guest_surveys.q3 + guest_surveys.q4 + guest_surveys.q5 + guest_surveys.q6 + guest_surveys.q7 + guest_surveys.q8 + guest_surveys.q9) / 9.0)';

        $applyFilters = function ($builder) use ($q, $avgMin, $from, $to, $avgExpr) {
            if ($q !== '') {
                $builder->where(function ($qb) use ($q) {
                    $qb
                        ->where('comment', 'like', "%{$q}%")
                        ->orWhereHas('visit', function ($v) use ($q) {
                            $v
                                ->where('name', 'like', "%{$q}%")
                                ->orWhere('purpose', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                });
            }

            if ($from !== '') {
                $builder->whereDate('submitted_at', '>=', $from);
            }
            if ($to !== '') {
                $builder->whereDate('submitted_at', '<=', $to);
            }

            if ($avgMin !== '') {
                $min = (float) $avgMin;
                $builder
                    ->whereNotNull('q1')
                    ->whereNotNull('q2')
                    ->whereNotNull('q3')
                    ->whereNotNull('q4')
                    ->whereNotNull('q5')
                    ->whereNotNull('q6')
                    ->whereNotNull('q7')
                    ->whereNotNull('q8')
                    ->whereNotNull('q9')
                    ->whereRaw("{$avgExpr} >= ?", [$min]);
            }
        };

        $summaryQuery = GuestSurvey::query()->with('visit');

        if (($user?->role ?? null) === 'admin_dinas') {
            $dinasId = (int) ($user->dinas_id ?? 0);
            if ($dinasId > 0) {
                $summaryQuery->whereHas('visit', fn($v) => $v->where('dinas_id', $dinasId));
            } else {
                $summaryQuery->whereRaw('1=0');
            }
        }
        $applyFilters($summaryQuery);
        $summaryQuery
            ->whereNotNull('q1')
            ->whereNotNull('q2')
            ->whereNotNull('q3')
            ->whereNotNull('q4')
            ->whereNotNull('q5')
            ->whereNotNull('q6')
            ->whereNotNull('q7')
            ->whereNotNull('q8')
            ->whereNotNull('q9');

        $agg = (clone $summaryQuery)
            ->selectRaw('COUNT(*) as n')
            ->selectRaw('AVG(q1) as avg_q1')
            ->selectRaw('AVG(q2) as avg_q2')
            ->selectRaw('AVG(q3) as avg_q3')
            ->selectRaw('AVG(q4) as avg_q4')
            ->selectRaw('AVG(q5) as avg_q5')
            ->selectRaw('AVG(q6) as avg_q6')
            ->selectRaw('AVG(q7) as avg_q7')
            ->selectRaw('AVG(q8) as avg_q8')
            ->selectRaw('AVG(q9) as avg_q9')
            ->first();

        $avgByKey = [
            'q1' => $agg?->avg_q1 !== null ? (float) $agg->avg_q1 : null,
            'q2' => $agg?->avg_q2 !== null ? (float) $agg->avg_q2 : null,
            'q3' => $agg?->avg_q3 !== null ? (float) $agg->avg_q3 : null,
            'q4' => $agg?->avg_q4 !== null ? (float) $agg->avg_q4 : null,
            'q5' => $agg?->avg_q5 !== null ? (float) $agg->avg_q5 : null,
            'q6' => $agg?->avg_q6 !== null ? (float) $agg->avg_q6 : null,
            'q7' => $agg?->avg_q7 !== null ? (float) $agg->avg_q7 : null,
            'q8' => $agg?->avg_q8 !== null ? (float) $agg->avg_q8 : null,
            'q9' => $agg?->avg_q9 !== null ? (float) $agg->avg_q9 : null,
        ];

        $overall = Ikm::fromAverages($avgByKey);

        $ikmSummary = [
            'n' => (int) ($agg?->n ?? 0),
            'avg_by_key' => $avgByKey,
            'overall_nrr' => (float) $overall['nrr'],
            'overall_ikm' => (float) $overall['ikm'],
            'mutu' => (string) $overall['mutu'],
            'kinerja' => (string) $overall['kinerja'],
        ];

        return view('admin.survey.ikm', [
            'ikmSummary' => $ikmSummary,
        ]);
    }

    public function exportIkmCsv(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $avgMin = (string) $request->query('avg_min', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $avgExpr = '((guest_surveys.q1 + guest_surveys.q2 + guest_surveys.q3 + guest_surveys.q4 + guest_surveys.q5 + guest_surveys.q6 + guest_surveys.q7 + guest_surveys.q8 + guest_surveys.q9) / 9.0)';

        $applyFilters = function ($builder) use ($q, $avgMin, $from, $to, $avgExpr) {
            if ($q !== '') {
                $builder->where(function ($qb) use ($q) {
                    $qb
                        ->where('comment', 'like', "%{$q}%")
                        ->orWhereHas('visit', function ($v) use ($q) {
                            $v
                                ->where('name', 'like', "%{$q}%")
                                ->orWhere('purpose', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                });
            }

            if ($from !== '') {
                $builder->whereDate('submitted_at', '>=', $from);
            }
            if ($to !== '') {
                $builder->whereDate('submitted_at', '<=', $to);
            }

            if ($avgMin !== '') {
                $min = (float) $avgMin;
                $builder
                    ->whereNotNull('q1')
                    ->whereNotNull('q2')
                    ->whereNotNull('q3')
                    ->whereNotNull('q4')
                    ->whereNotNull('q5')
                    ->whereNotNull('q6')
                    ->whereNotNull('q7')
                    ->whereNotNull('q8')
                    ->whereNotNull('q9')
                    ->whereRaw("{$avgExpr} >= ?", [$min]);
            }
        };

        $summaryQuery = GuestSurvey::query()->with('visit');
        $this->scopeByDinas($request, $summaryQuery);
        $applyFilters($summaryQuery);
        $summaryQuery
            ->whereNotNull('q1')
            ->whereNotNull('q2')
            ->whereNotNull('q3')
            ->whereNotNull('q4')
            ->whereNotNull('q5')
            ->whereNotNull('q6')
            ->whereNotNull('q7')
            ->whereNotNull('q8')
            ->whereNotNull('q9');

        $agg = (clone $summaryQuery)
            ->selectRaw('COUNT(*) as n')
            ->selectRaw('AVG(q1) as avg_q1')
            ->selectRaw('AVG(q2) as avg_q2')
            ->selectRaw('AVG(q3) as avg_q3')
            ->selectRaw('AVG(q4) as avg_q4')
            ->selectRaw('AVG(q5) as avg_q5')
            ->selectRaw('AVG(q6) as avg_q6')
            ->selectRaw('AVG(q7) as avg_q7')
            ->selectRaw('AVG(q8) as avg_q8')
            ->selectRaw('AVG(q9) as avg_q9')
            ->first();

        $avgByKey = [
            'q1' => $agg?->avg_q1 !== null ? (float) $agg->avg_q1 : null,
            'q2' => $agg?->avg_q2 !== null ? (float) $agg->avg_q2 : null,
            'q3' => $agg?->avg_q3 !== null ? (float) $agg->avg_q3 : null,
            'q4' => $agg?->avg_q4 !== null ? (float) $agg->avg_q4 : null,
            'q5' => $agg?->avg_q5 !== null ? (float) $agg->avg_q5 : null,
            'q6' => $agg?->avg_q6 !== null ? (float) $agg->avg_q6 : null,
            'q7' => $agg?->avg_q7 !== null ? (float) $agg->avg_q7 : null,
            'q8' => $agg?->avg_q8 !== null ? (float) $agg->avg_q8 : null,
            'q9' => $agg?->avg_q9 !== null ? (float) $agg->avg_q9 : null,
        ];

        $overall = Ikm::fromAverages($avgByKey);
        $n = (int) ($agg?->n ?? 0);
        $overallNrr = (float) $overall['nrr'];
        $overallIkm = (float) $overall['ikm'];
        $mutu = (string) $overall['mutu'];
        $kinerja = (string) $overall['kinerja'];

        $labels = [
            'q1' => 'Kesesuaian persyaratan pelayanan',
            'q2' => 'Kemudahan prosedur',
            'q3' => 'Kecepatan waktu pelayanan',
            'q4' => 'Kewajaran biaya/tarif',
            'q5' => 'Kesesuaian produk pelayanan',
            'q6' => 'Kompetensi petugas',
            'q7' => 'Perilaku petugas (sopan/ramah)',
            'q8' => 'Kualitas sarana & prasarana',
            'q9' => 'Penanganan pengaduan',
        ];

        $tmp = fopen('php://temp', 'w+');
        // UTF-8 BOM for Excel
        fwrite($tmp, "\xEF\xBB\xBF");

        $now = now()->format('Y-m-d H:i:s');

        fputcsv($tmp, ['Rekap IKM Survey Pelayanan', 'PermenPANRB No. 14 Tahun 2017'], ';');
        fputcsv($tmp, ['Generated At', $now], ';');
        fputcsv($tmp, ['Filter q', $q], ';');
        fputcsv($tmp, ['Filter avg_min', $avgMin], ';');
        fputcsv($tmp, ['Filter from', $from], ';');
        fputcsv($tmp, ['Filter to', $to], ';');
        fputcsv($tmp, ['Total Respon (Q1..Q9 lengkap)', $n], ';');
        fputcsv($tmp, ['NRR (1-4)', number_format($overallNrr, 4, '.', '')], ';');
        fputcsv($tmp, ['IKM (25-100)', number_format($overallIkm, 4, '.', '')], ';');
        fputcsv($tmp, ['Mutu', $mutu], ';');
        fputcsv($tmp, ['Kinerja', $kinerja], ';');
        fputcsv($tmp, [], ';');

        fputcsv($tmp, ['Unsur', 'Label', 'NRR (1-4)', 'Konversi (x25)'], ';');
        foreach ($labels as $key => $label) {
            $v = $avgByKey[$key] ?? null;
            $nrr = $v !== null ? (float) $v : null;
            $conv = $nrr !== null ? $nrr * 25.0 : null;
            fputcsv($tmp, [
                strtoupper($key),
                $label,
                $nrr !== null ? number_format($nrr, 4, '.', '') : '',
                $conv !== null ? number_format($conv, 4, '.', '') : '',
            ], ';');
        }

        rewind($tmp);
        $csv = stream_get_contents($tmp);
        fclose($tmp);

        $filename = 'rekap-ikm-survey-' . now()->format('Ymd-His') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportDetailCsv(Request $request)
    {
        $actor = $request->user();
        $role = (string) ($actor?->role ?? '');
        $actorDinasId = (int) ($actor->dinas_id ?? 0);
        $dinasIdFilter = (int) $request->query('dinas_id', 0);
        if (!in_array($role, ['super_admin', 'admin_dinas'], true)) {
            abort(403);
        }

        $q = trim((string) $request->query('q', ''));
        $avgMin = (string) $request->query('avg_min', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $avgExpr = '((guest_surveys.q1 + guest_surveys.q2 + guest_surveys.q3 + guest_surveys.q4 + guest_surveys.q5 + guest_surveys.q6 + guest_surveys.q7 + guest_surveys.q8 + guest_surveys.q9) / 9.0)';

        $applyFilters = function ($builder) use ($q, $avgMin, $from, $to, $avgExpr) {
            if ($q !== '') {
                $builder->where(function ($qb) use ($q) {
                    $qb
                        ->where('guest_surveys.comment', 'like', "%{$q}%")
                        ->orWhere('guest_visits.name', 'like', "%{$q}%")
                        ->orWhere('guest_visits.purpose', 'like', "%{$q}%")
                        ->orWhere('guest_visits.email', 'like', "%{$q}%");
                });
            }

            if ($from !== '') {
                $builder->whereDate('guest_surveys.submitted_at', '>=', $from);
            }
            if ($to !== '') {
                $builder->whereDate('guest_surveys.submitted_at', '<=', $to);
            }

            if ($avgMin !== '') {
                $min = (float) $avgMin;
                $builder
                    ->whereNotNull('guest_surveys.q1')
                    ->whereNotNull('guest_surveys.q2')
                    ->whereNotNull('guest_surveys.q3')
                    ->whereNotNull('guest_surveys.q4')
                    ->whereNotNull('guest_surveys.q5')
                    ->whereNotNull('guest_surveys.q6')
                    ->whereNotNull('guest_surveys.q7')
                    ->whereNotNull('guest_surveys.q8')
                    ->whereNotNull('guest_surveys.q9')
                    ->whereRaw("{$avgExpr} >= ?", [$min]);
            }
        };

        $filename = 'detail-survey-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($applyFilters, $role, $actorDinasId, $dinasIdFilter) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            $delimiter = ';';

            fputcsv($out, [
                'submitted_at',
                'guest_name',
                'email',
                'purpose',
                'service_type',
                'q1',
                'q2',
                'q3',
                'q4',
                'q5',
                'q6',
                'q7',
                'q8',
                'q9',
                'nrr_avg',
                'ikm_x25',
                'comment',
            ], $delimiter);

            $query = GuestSurvey::query()
                ->join('guest_visits', 'guest_visits.id', '=', 'guest_surveys.visit_id')
                ->select([
                    'guest_surveys.id',
                    'guest_surveys.submitted_at',
                    'guest_surveys.comment',
                    'guest_surveys.q1',
                    'guest_surveys.q2',
                    'guest_surveys.q3',
                    'guest_surveys.q4',
                    'guest_surveys.q5',
                    'guest_surveys.q6',
                    'guest_surveys.q7',
                    'guest_surveys.q8',
                    'guest_surveys.q9',
                    'guest_visits.name as guest_name',
                    'guest_visits.email',
                    'guest_visits.purpose',
                    'guest_visits.service_type',
                ])
                ->orderBy('guest_surveys.id', 'asc');

            if ($role === 'admin_dinas') {
                if ($actorDinasId > 0) {
                    $query->where('guest_visits.dinas_id', $actorDinasId);
                } else {
                    $query->whereRaw('1=0');
                }
            } elseif ($dinasIdFilter > 0) {
                $query->where('guest_visits.dinas_id', $dinasIdFilter);
            }

            $applyFilters($query);

            $query->chunkById(500, function ($rows) use ($out, $delimiter) {
                foreach ($rows as $row) {
                    $vals = [
                        (string) ($row->submitted_at ?? ''),
                        (string) ($row->guest_name ?? ''),
                        (string) ($row->email ?? ''),
                        (string) ($row->purpose ?? ''),
                        (string) ($row->service_type ?? ''),
                        (string) ($row->q1 ?? ''),
                        (string) ($row->q2 ?? ''),
                        (string) ($row->q3 ?? ''),
                        (string) ($row->q4 ?? ''),
                        (string) ($row->q5 ?? ''),
                        (string) ($row->q6 ?? ''),
                        (string) ($row->q7 ?? ''),
                        (string) ($row->q8 ?? ''),
                        (string) ($row->q9 ?? ''),
                    ];

                    $nums = [];
                    foreach (range(1, 9) as $i) {
                        $k = "q{$i}";
                        if ($row->$k === null) {
                            $nums = [];
                            break;
                        }
                        $nums[] = (float) $row->$k;
                    }

                    $avg = '';
                    $ikm = '';
                    if (count($nums) === 9) {
                        $avgVal = array_sum($nums) / 9.0;
                        $avg = number_format($avgVal, 4, '.', '');
                        $ikm = number_format($avgVal * 25.0, 4, '.', '');
                    }

                    $comment = (string) ($row->comment ?? '');
                    $comment = Str::of($comment)->replace(["\r\n", "\r"], "\n")->toString();

                    fputcsv($out, array_merge($vals, [$avg, $ikm, $comment]), $delimiter);
                }
            }, 'guest_surveys.id');

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $avgMin = (string) $request->query('avg_min', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $avgExpr = '((guest_surveys.q1 + guest_surveys.q2 + guest_surveys.q3 + guest_surveys.q4 + guest_surveys.q5 + guest_surveys.q6 + guest_surveys.q7 + guest_surveys.q8 + guest_surveys.q9) / 9.0)';

        $applyFilters = function ($builder) use ($q, $avgMin, $from, $to, $avgExpr) {
            if ($q !== '') {
                $builder->where(function ($qb) use ($q) {
                    $qb
                        ->where('guest_surveys.comment', 'like', "%{$q}%")
                        ->orWhereHas('visit', function ($v) use ($q) {
                            $v
                                ->where('name', 'like', "%{$q}%")
                                ->orWhere('purpose', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                });
            }

            if ($from !== '') {
                $builder->whereDate('submitted_at', '>=', $from);
            }
            if ($to !== '') {
                $builder->whereDate('submitted_at', '<=', $to);
            }

            if ($avgMin !== '') {
                $min = (float) $avgMin;
                $builder
                    ->whereNotNull('q1')
                    ->whereNotNull('q2')
                    ->whereNotNull('q3')
                    ->whereNotNull('q4')
                    ->whereNotNull('q5')
                    ->whereNotNull('q6')
                    ->whereNotNull('q7')
                    ->whereNotNull('q8')
                    ->whereNotNull('q9')
                    ->whereRaw("{$avgExpr} >= ?", [$min]);
            }
        };

        // IKM summary
        $summaryQuery = GuestSurvey::query()->with('visit');
        $this->scopeByDinas($request, $summaryQuery);
        $applyFilters($summaryQuery);
        $summaryQuery
            ->whereNotNull('q1')
            ->whereNotNull('q2')
            ->whereNotNull('q3')
            ->whereNotNull('q4')
            ->whereNotNull('q5')
            ->whereNotNull('q6')
            ->whereNotNull('q7')
            ->whereNotNull('q8')
            ->whereNotNull('q9');

        $agg = (clone $summaryQuery)
            ->selectRaw('COUNT(*) as n')
            ->selectRaw('AVG(q1) as avg_q1')
            ->selectRaw('AVG(q2) as avg_q2')
            ->selectRaw('AVG(q3) as avg_q3')
            ->selectRaw('AVG(q4) as avg_q4')
            ->selectRaw('AVG(q5) as avg_q5')
            ->selectRaw('AVG(q6) as avg_q6')
            ->selectRaw('AVG(q7) as avg_q7')
            ->selectRaw('AVG(q8) as avg_q8')
            ->selectRaw('AVG(q9) as avg_q9')
            ->first();

        $avgByKey = [
            'q1' => $agg?->avg_q1 !== null ? (float) $agg->avg_q1 : null,
            'q2' => $agg?->avg_q2 !== null ? (float) $agg->avg_q2 : null,
            'q3' => $agg?->avg_q3 !== null ? (float) $agg->avg_q3 : null,
            'q4' => $agg?->avg_q4 !== null ? (float) $agg->avg_q4 : null,
            'q5' => $agg?->avg_q5 !== null ? (float) $agg->avg_q5 : null,
            'q6' => $agg?->avg_q6 !== null ? (float) $agg->avg_q6 : null,
            'q7' => $agg?->avg_q7 !== null ? (float) $agg->avg_q7 : null,
            'q8' => $agg?->avg_q8 !== null ? (float) $agg->avg_q8 : null,
            'q9' => $agg?->avg_q9 !== null ? (float) $agg->avg_q9 : null,
        ];

        $overall = Ikm::fromAverages($avgByKey);
        $ikmSummary = [
            'n' => (int) ($agg?->n ?? 0),
            'avg_by_key' => $avgByKey,
            'overall_nrr' => (float) $overall['nrr'],
            'overall_ikm' => (float) $overall['ikm'],
            'mutu' => (string) $overall['mutu'],
            'kinerja' => (string) $overall['kinerja'],
        ];

        // Detail rows (limit to keep PDF size sane)
        $maxRows = 200;
        $rowsQuery = GuestSurvey::query()->with('visit')->orderBy('submitted_at', 'desc');
        $this->scopeByDinas($request, $rowsQuery);
        $applyFilters($rowsQuery);
        $surveys = $rowsQuery->limit($maxRows)->get();

        $generatedAt = now();
        $filters = [
            'q' => $q,
            'avg_min' => $avgMin,
            'from' => $from,
            'to' => $to,
        ];

        $html = view('admin.survey.export_pdf', [
            'generatedAt' => $generatedAt,
            'filters' => $filters,
            'ikmSummary' => $ikmSummary,
            'surveys' => $surveys,
            'maxRows' => $maxRows,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'laporan-survey-' . now()->format('Ymd-His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportIkmPdf(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $avgMin = (string) $request->query('avg_min', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $avgExpr = '((guest_surveys.q1 + guest_surveys.q2 + guest_surveys.q3 + guest_surveys.q4 + guest_surveys.q5 + guest_surveys.q6 + guest_surveys.q7 + guest_surveys.q8 + guest_surveys.q9) / 9.0)';

        $applyFilters = function ($builder) use ($q, $avgMin, $from, $to, $avgExpr) {
            if ($q !== '') {
                $builder->where(function ($qb) use ($q) {
                    $qb
                        ->where('comment', 'like', "%{$q}%")
                        ->orWhereHas('visit', function ($v) use ($q) {
                            $v
                                ->where('name', 'like', "%{$q}%")
                                ->orWhere('purpose', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                });
            }

            if ($from !== '') {
                $builder->whereDate('submitted_at', '>=', $from);
            }
            if ($to !== '') {
                $builder->whereDate('submitted_at', '<=', $to);
            }

            if ($avgMin !== '') {
                $min = (float) $avgMin;
                $builder
                    ->whereNotNull('q1')
                    ->whereNotNull('q2')
                    ->whereNotNull('q3')
                    ->whereNotNull('q4')
                    ->whereNotNull('q5')
                    ->whereNotNull('q6')
                    ->whereNotNull('q7')
                    ->whereNotNull('q8')
                    ->whereNotNull('q9')
                    ->whereRaw("{$avgExpr} >= ?", [$min]);
            }
        };

        $summaryQuery = GuestSurvey::query()->with('visit');
        $this->scopeByDinas($request, $summaryQuery);
        $applyFilters($summaryQuery);
        $summaryQuery
            ->whereNotNull('q1')
            ->whereNotNull('q2')
            ->whereNotNull('q3')
            ->whereNotNull('q4')
            ->whereNotNull('q5')
            ->whereNotNull('q6')
            ->whereNotNull('q7')
            ->whereNotNull('q8')
            ->whereNotNull('q9');

        $agg = (clone $summaryQuery)
            ->selectRaw('COUNT(*) as n')
            ->selectRaw('AVG(q1) as avg_q1')
            ->selectRaw('AVG(q2) as avg_q2')
            ->selectRaw('AVG(q3) as avg_q3')
            ->selectRaw('AVG(q4) as avg_q4')
            ->selectRaw('AVG(q5) as avg_q5')
            ->selectRaw('AVG(q6) as avg_q6')
            ->selectRaw('AVG(q7) as avg_q7')
            ->selectRaw('AVG(q8) as avg_q8')
            ->selectRaw('AVG(q9) as avg_q9')
            ->first();

        $avgByKey = [
            'q1' => $agg?->avg_q1 !== null ? (float) $agg->avg_q1 : null,
            'q2' => $agg?->avg_q2 !== null ? (float) $agg->avg_q2 : null,
            'q3' => $agg?->avg_q3 !== null ? (float) $agg->avg_q3 : null,
            'q4' => $agg?->avg_q4 !== null ? (float) $agg->avg_q4 : null,
            'q5' => $agg?->avg_q5 !== null ? (float) $agg->avg_q5 : null,
            'q6' => $agg?->avg_q6 !== null ? (float) $agg->avg_q6 : null,
            'q7' => $agg?->avg_q7 !== null ? (float) $agg->avg_q7 : null,
            'q8' => $agg?->avg_q8 !== null ? (float) $agg->avg_q8 : null,
            'q9' => $agg?->avg_q9 !== null ? (float) $agg->avg_q9 : null,
        ];

        $overall = Ikm::fromAverages($avgByKey);
        $ikmSummary = [
            'n' => (int) ($agg?->n ?? 0),
            'avg_by_key' => $avgByKey,
            'overall_nrr' => (float) $overall['nrr'],
            'overall_ikm' => (float) $overall['ikm'],
            'mutu' => (string) $overall['mutu'],
            'kinerja' => (string) $overall['kinerja'],
        ];

        $generatedAt = now();
        $filters = [
            'q' => $q,
            'avg_min' => $avgMin,
            'from' => $from,
            'to' => $to,
        ];

        $html = view('admin.survey.export_ikm_pdf', [
            'generatedAt' => $generatedAt,
            'filters' => $filters,
            'ikmSummary' => $ikmSummary,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'rekap-ikm-survey-' . now()->format('Ymd-His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $avgMin = (string) $request->query('avg_min', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $avgExpr = '((guest_surveys.q1 + guest_surveys.q2 + guest_surveys.q3 + guest_surveys.q4 + guest_surveys.q5 + guest_surveys.q6 + guest_surveys.q7 + guest_surveys.q8 + guest_surveys.q9) / 9.0)';

        $applyFilters = function ($builder) use ($q, $avgMin, $from, $to, $avgExpr) {
            if ($q !== '') {
                $builder->where(function ($qb) use ($q) {
                    $qb
                        ->where('comment', 'like', "%{$q}%")
                        ->orWhereHas('visit', function ($v) use ($q) {
                            $v
                                ->where('name', 'like', "%{$q}%")
                                ->orWhere('purpose', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                });
            }

            if ($from !== '') {
                $builder->whereDate('submitted_at', '>=', $from);
            }
            if ($to !== '') {
                $builder->whereDate('submitted_at', '<=', $to);
            }

            if ($avgMin !== '') {
                $min = (float) $avgMin;
                $builder
                    ->whereNotNull('q1')
                    ->whereNotNull('q2')
                    ->whereNotNull('q3')
                    ->whereNotNull('q4')
                    ->whereNotNull('q5')
                    ->whereNotNull('q6')
                    ->whereNotNull('q7')
                    ->whereNotNull('q8')
                    ->whereNotNull('q9')
                    ->whereRaw("{$avgExpr} >= ?", [$min]);
            }
        };

        $maxRows = 5000;
        $rowsQuery = GuestSurvey::query()->with('visit')->orderBy('submitted_at', 'desc');
        $this->scopeByDinas($request, $rowsQuery);
        $applyFilters($rowsQuery);
        $surveys = $rowsQuery->limit($maxRows)->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Sistem Absensi & Buku Tamu')
            ->setTitle('Laporan Survey');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Survey');

        $sheet->fromArray([
            ['Laporan Survey'],
            ['Generated At', now()->format('Y-m-d H:i:s')],
            ['Filter q', $q],
            ['Filter avg_min', $avgMin],
            ['Filter from', $from],
            ['Filter to', $to],
            [],
            ['submitted_at', 'guest_name', 'email', 'purpose', 'service_type', 'q1', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7', 'q8', 'q9', 'nrr_avg', 'ikm_x25', 'comment'],
        ]);

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A8:Q8')->applyFromArray([
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
        foreach ($surveys as $s) {
            $visit = $s->visit;
            $nums = [];
            foreach (range(1, 9) as $i) {
                $k = "q{$i}";
                if ($s->$k === null) {
                    $nums = [];
                    break;
                }
                $nums[] = (float) $s->$k;
            }

            $avg = '';
            $ikm = '';
            if (count($nums) === 9) {
                $avgVal = array_sum($nums) / 9.0;
                $avg = round($avgVal, 4);
                $ikm = round($avgVal * 25.0, 4);
            }

            $submittedVal = '';
            if (!empty($s->submitted_at)) {
                $submittedVal = ExcelDate::PHPToExcel($s->submitted_at);
            }

            $sheet->fromArray([
                $submittedVal,
                (string) ($visit?->name ?? ''),
                (string) ($visit?->email ?? ''),
                (string) ($visit?->purpose ?? ''),
                (string) ($visit?->service_type ?? ''),
                $s->q1,
                $s->q2,
                $s->q3,
                $s->q4,
                $s->q5,
                $s->q6,
                $s->q7,
                $s->q8,
                $s->q9,
                $avg,
                $ikm,
                (string) ($s->comment ?? ''),
            ], null, "A{$row}");
            $row++;
        }

        $lastRow = max(8, $row - 1);
        $sheet->setAutoFilter("A8:Q{$lastRow}");
        if ($lastRow >= 9) {
            $sheet->getStyle("D9:D{$lastRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("Q9:Q{$lastRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("F9:N{$lastRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $sheet->getStyle("O9:P{$lastRow}")->getNumberFormat()->setFormatCode('0.0000');
            $sheet->getStyle("A9:A{$lastRow}")->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
        }

        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'laporan-survey-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
