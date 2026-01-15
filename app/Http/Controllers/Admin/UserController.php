<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Support\AppSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // ===== Edit mode =====
        $editUserId = $request->query('edit');
        $editUser = null;
        if ($editUserId !== null && $editUserId !== '') {
            $editUser = User::query()->find($editUserId);
        }

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

        return view('admin.users.index', [
            'users' => $users,
            'editUser' => $editUser,
            'scoring' => $scoring,
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
            $payload['score_override'] = $validated['score_override'] ?? null;
            $payload['score_override_note'] = $validated['score_override_note'] ?? null;
        } else {
            $payload['intern_status'] = 'aktif';
            $payload['internship_start_date'] = null;
            $payload['internship_end_date'] = null;
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
