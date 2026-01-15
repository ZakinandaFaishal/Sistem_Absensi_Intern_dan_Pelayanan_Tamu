<?php

namespace App\Http\Controllers;

use App\Models\GuestVisit;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'institution' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'job' => ['nullable', 'string', 'max:120'],
            'jabatan' => ['nullable', 'string', 'max:120'],
            'service_type' => ['required', 'string', Rule::in(['layanan', 'koordinasi', 'berkas', 'lainnya'])],
            'purpose_detail' => ['required', 'string', 'max:500'],
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

        // guest_visits.purpose is a string (VARCHAR 255). Keep it safe to avoid DB errors.
        $purpose = Str::of($purpose)->limit(255, '')->toString();

        $visit = GuestVisit::query()->create([
            'name' => $validated['name'],
            'institution' => $validated['institution'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'purpose' => $purpose,
            'arrived_at' => CarbonImmutable::now(),
        ]);

        return view('guest.thanks', [
            'visit' => $visit,
        ]);
    }

    public function index(Request $request)
    {
        // ambil filter/sort dari query string (sesuai blade)
        $q      = $request->query('q', '');
        $status = $request->query('status', ''); // '', 'pending', 'done'
        $from   = $request->query('from', '');
        $to     = $request->query('to', '');
        $sort   = $request->query('sort', 'arrived_at');
        $dir    = $request->query('dir', 'desc');

        // whitelist sorting biar aman
        $allowedSort = ['arrived_at', 'completed_at', 'name'];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'arrived_at';
        }
        $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';

        $query = GuestVisit::query();

        // search (nama / purpose)
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                ->orWhere('purpose', 'like', "%{$q}%");
            });
        }

        // status filter
        if ($status === 'pending') {
            $query->whereNull('completed_at');
        } elseif ($status === 'done') {
            $query->whereNotNull('completed_at');
        }

        // date range filter (pakai arrived_at)
        if ($from) {
            $query->whereDate('arrived_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('arrived_at', '<=', $to);
        }

        // sorting
        $query->orderBy($sort, $dir);

        // paginate + pertahankan query params agar links() ikut filter/sort
        $visits = $query->paginate(10)->appends($request->query());

        return view('admin.guest.index', [
            'visits' => $visits,
        ]);
    }


    public function complete(Request $request, GuestVisit $visit)
    {
        if ($visit->completed_at !== null) {
            return redirect()
                ->route('admin.guest.index')
                ->with('status', 'Kunjungan sudah ditandai selesai.');
        }

        $visit->fill([
            'completed_at' => CarbonImmutable::now(),
            'handled_by' => $request->user()->id,
        ])->save();

        return redirect()
            ->route('admin.guest.index')
            ->with('status', 'Kunjungan berhasil ditandai selesai.');
    }

    public function active()
    {
        $visits = \App\Models\GuestVisit::query()
            ->whereNull('completed_at')
            ->orderByDesc('arrived_at')
            ->limit(20)
            ->get(['id', 'name', 'purpose', 'arrived_at', 'completed_at']);

        return response()->json([
            'data' => $visits->map(function ($v) {
                return [
                    'id' => $v->id,
                    'name' => $v->name,
                    'purpose' => $v->purpose,
                    'arrived_at' => optional($v->arrived_at)->format('d M Y H:i') ?? (string) $v->arrived_at,
                    'status' => 'Sedang berkunjung',
                ];
            })->values(),
        ]);
    }
}
