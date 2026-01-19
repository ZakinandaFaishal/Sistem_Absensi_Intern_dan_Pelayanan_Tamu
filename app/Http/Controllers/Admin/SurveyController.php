<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestSurvey;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $avgMin = $request->query('avg_min', '');
        $from   = $request->query('from', '');
        $to     = $request->query('to', '');

        $sort = (string) $request->query('sort', 'submitted_at');
        $dir  = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // kolom yang diizinkan untuk sorting
        $allowedSort = ['submitted_at', 'name', 'avg'];

        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'submitted_at';
        }

        $query = GuestSurvey::query()
            ->with(['visit']);

        // ===== Search (nama tamu / purpose / comment) =====
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('comment', 'like', "%{$q}%")
                  ->orWhereHas('visit', function ($v) use ($q) {
                      $v->where('name', 'like', "%{$q}%")
                        ->orWhere('purpose', 'like', "%{$q}%");
                  });
            });
        }

        // ===== Date range (submitted_at) =====
        if ($from !== '') {
            try {
                $fromDate = Carbon::parse($from)->startOfDay();
                $query->where('submitted_at', '>=', $fromDate);
            } catch (\Throwable $e) {}
        }

        if ($to !== '') {
            try {
                $toDate = Carbon::parse($to)->endOfDay();
                $query->where('submitted_at', '<=', $toDate);
            } catch (\Throwable $e) {}
        }

        // ===== avg_min (rata-rata Q1..Q9) =====
        // avg = (q1+...+q9) / 9
        if ($avgMin !== '' && is_numeric($avgMin)) {
            $avgMin = (float) $avgMin;

            // sum q1..q9 (anggap q1..q9 selalu numeric)
            $sumExpr = "(COALESCE(q1,0)+COALESCE(q2,0)+COALESCE(q3,0)+COALESCE(q4,0)+COALESCE(q5,0)+COALESCE(q6,0)+COALESCE(q7,0)+COALESCE(q8,0)+COALESCE(q9,0))";
            $avgExpr = "($sumExpr / 9)";

            $query->whereRaw("$avgExpr >= ?", [$avgMin]);
        }

        // ===== Sorting =====
        if ($sort === 'name') {
            // sort by visit.name
            $query->leftJoin('guest_visits', 'guest_visits.id', '=', 'guest_surveys.visit_id')
                  ->select('guest_surveys.*') // penting biar modelnya tetap GuestSurvey
                  ->orderBy('guest_visits.name', $dir);
        } elseif ($sort === 'avg') {
            $sumExpr = "(COALESCE(q1,0)+COALESCE(q2,0)+COALESCE(q3,0)+COALESCE(q4,0)+COALESCE(q5,0)+COALESCE(q6,0)+COALESCE(q7,0)+COALESCE(q8,0)+COALESCE(q9,0))";
            $avgExpr = "($sumExpr / 9)";

            $query->orderByRaw("$avgExpr {$dir}");
        } else {
            // submitted_at
            $query->orderBy('submitted_at', $dir);
        }

        $surveys = $query
            ->paginate(20)
            ->withQueryString();

        return view('admin.survey.index', [
            'surveys' => $surveys,
        ]);
    }
}
