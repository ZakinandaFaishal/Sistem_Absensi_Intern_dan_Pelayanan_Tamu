@extends('layouts.admin')

@section('title', 'User Management')
@section('page_title', 'User Management')

@section('content')

    @php
        // ====== FILTER & SORT STATE (dari query string) ======
        $q = request('q', '');
        $role = request('role', '');
        $sort = request('sort', 'created_at');
        $dir = request('dir', 'desc');

        $dinasNameById = [];
        foreach ($dinasOptions ?? [] as $d) {
            $dinasNameById[(string) $d->id] = $d->name;
        }

        $mergeQuery = function (array $extra = []) {
            return url()->current() . '?' . http_build_query(array_merge(request()->query(), $extra));
        };

        $sortUrl = function (string $col) use ($sort, $dir, $mergeQuery) {
            $nextDir = $sort === $col && $dir === 'asc' ? 'desc' : 'asc';
            return $mergeQuery(['sort' => $col, 'dir' => $nextDir, 'page' => 1]);
        };

        $sortIcon = function (string $col) use ($sort, $dir) {
            if ($sort !== $col) {
                return '↕';
            }
            return $dir === 'asc' ? '↑' : '↓';
        };
    @endphp

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">User Management</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola akun peserta magang, admin dinas, & super admin.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- EXPORT LAPORAN --}}
            <div class="relative">
                <button type="button" id="btnExportUsers"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                           shadow-sm shadow-slate-900/10 hover:bg-slate-800 hover:shadow-md hover:shadow-slate-900/10
                           transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                    <span>Export Laporan</span>
                    <span class="inline-block transition" id="exportUsersChevron">▾</span>
                </button>

                <div id="menuExportUsers"
                    class="hidden absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden z-50 ring-1 ring-slate-200/60">
                    <button type="button"
                        class="export-users-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between transition"
                        data-url="{{ route('admin.users.export.excel', request()->query()) }}" data-label="Excel">
                        <span>Export Excel (Users)</span>
                        <span class="text-xs text-slate-400">.xlsx</span>
                    </button>

                    <button type="button"
                        class="export-users-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between transition"
                        data-url="{{ route('admin.users.export.pdf', request()->query()) }}" data-label="PDF">
                        <span>Export PDF (Users)</span>
                        <span class="text-xs text-slate-400">.pdf</span>
                    </button>
                </div>
            </div>

            <iframe id="dlUsersFrame" class="hidden"></iframe>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                       shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5
                       transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="pt-5 space-y-6">
        @if (session('status'))
            <div
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm shadow-emerald-900/5">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div
                class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm shadow-rose-900/5">
                <div class="font-semibold">Terjadi kesalahan</div>
                <ul class="mt-1 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- TABLE CARD --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/5 overflow-hidden">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200 bg-gradient-to-b from-white to-slate-50/30">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 ring-1 ring-slate-200/70">
                        <x-icon name="users" class="h-5 w-5 text-slate-700" />
                    </span>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Daftar Users</h3>
                        <p class="text-xs text-slate-500">Kelola status dan aksi akun.</p>
                    </div>
                </div>

                <div class="text-xs text-slate-500">
                    Total halaman: <span class="font-semibold text-slate-700">{{ $users->lastPage() }}</span>
                    • Total data: <span class="font-semibold text-slate-700">{{ $users->total() }}</span>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="px-6 pt-5">
                <form id="usersFilterForm" method="GET" action="{{ route('admin.users.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-12 gap-3">

                    <input type="hidden" name="page" value="1">
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="dir" value="{{ $dir }}">

                    <div class="sm:col-span-5">
                        <label class="block text-xs font-semibold text-slate-700">Cari</label>
                        <input id="usersQInput" type="text" name="q" value="{{ $q }}"
                            placeholder="Nama / email / username / NIK / telepon…"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   shadow-sm shadow-slate-900/5
                                   focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-700">Role</label>
                        <select id="usersRoleSelect" name="role"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   shadow-sm shadow-slate-900/5
                                   focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition">
                            <option value="" @selected($role === '')>Semua</option>
                            <option value="intern" @selected($role === 'intern')>intern</option>
                            <option value="admin_dinas" @selected($role === 'admin_dinas')>admin_dinas</option>
                        </select>
                    </div>

                    <div class="sm:col-span-4 flex items-end justify-between gap-2">
                        <div class="w-full text-xs text-slate-500">
                            @if ($q || $role !== '')
                                <span class="font-semibold text-slate-700">Filter aktif</span>
                                <span class="text-slate-400">•</span>
                                <a href="{{ url()->current() . '?' . http_build_query(array_diff_key(request()->query(), array_flip(['q','role','page']))) }}"
                                    class="font-semibold text-blue-700 hover:text-blue-800 underline underline-offset-2">
                                    Reset
                                </a>
                            @else
                                Filter diterapkan otomatis.
                            @endif
                        </div>
                    </div>
                </form>

                {{-- SORT CHIPS (gaya presensi) --}}
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <div class="text-xs font-semibold text-slate-500">Urutkan:</div>

                    <a href="{{ $sortUrl('created_at') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700
                               shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5 transition">
                        Dibuat <span class="text-slate-400">{{ $sortIcon('created_at') }}</span>
                    </a>

                    <a href="{{ $sortUrl('name') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700
                               shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5 transition">
                        Nama <span class="text-slate-400">{{ $sortIcon('name') }}</span>
                    </a>

                    <a href="{{ $sortUrl('attended_days') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700
                               shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5 transition">
                        Presensi <span class="text-slate-400">{{ $sortIcon('attended_days') }}</span>
                    </a>
                </div>
            </div>

            <script>
                (function() {
                    const form = document.getElementById('usersFilterForm');
                    const qInput = document.getElementById('usersQInput');
                    const roleSelect = document.getElementById('usersRoleSelect');
                    if (!form || !qInput || !roleSelect) return;

                    let timer = null;
                    let isComposing = false;

                    const submit = () => {
                        const pageInput = form.querySelector('input[name="page"]');
                        if (pageInput) pageInput.value = '1';
                        form.submit();
                    };

                    const debounceSubmit = () => {
                        if (isComposing) return;
                        if (timer) window.clearTimeout(timer);
                        timer = window.setTimeout(submit, 900);
                    };

                    qInput.addEventListener('compositionstart', () => { isComposing = true; });
                    qInput.addEventListener('compositionend', () => { isComposing = false; debounceSubmit(); });
                    qInput.addEventListener('input', debounceSubmit);

                    roleSelect.addEventListener('change', submit);
                })();
            </script>

            <div class="p-6">
                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-slate-600 border-b border-slate-200">
                                <th class="py-3 px-4 font-semibold">Nama</th>
                                <th class="py-3 px-4 font-semibold">Username</th>
                                <th class="py-3 px-4 font-semibold">NIK</th>
                                <th class="py-3 px-4 font-semibold">No. Telepon</th>
                                <th class="py-3 px-4 font-semibold">Email</th>
                                <th class="py-3 px-4 font-semibold">Role</th>
                                <th class="py-3 px-4 font-semibold">Dinas</th>
                                <th class="py-3 px-4 font-semibold">Status</th>
                                <th class="py-3 px-4 font-semibold">Nilai</th>
                                <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($users as $user)
                                @php
                                    $uRole = $user->role ?? 'intern';
                                    $rolePill =
                                        $uRole === 'super_admin'
                                            ? 'bg-slate-100 text-slate-700 ring-slate-200'
                                            : ($uRole === 'admin_dinas'
                                                ? 'bg-indigo-50 text-indigo-700 ring-indigo-100'
                                                : 'bg-emerald-50 text-emerald-700 ring-emerald-100');

                                    $dinasName =
                                        $user->dinas_id !== null
                                            ? $dinasNameById[(string) $user->dinas_id] ?? null
                                            : null;

                                    $st = $user->intern_status ?? 'aktif';
                                    $statusPill = $st === 'tamat'
                                        ? 'bg-rose-50 text-rose-700 ring-rose-100'
                                        : 'bg-emerald-50 text-emerald-700 ring-emerald-100';
                                @endphp

                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                    </td>

                                    <td class="py-3 px-4 whitespace-nowrap text-slate-700">{{ $user->username ?? '—' }}</td>
                                    <td class="py-3 px-4 whitespace-nowrap text-slate-700">{{ $user->nik ?? '—' }}</td>
                                    <td class="py-3 px-4 whitespace-nowrap text-slate-700">{{ $user->phone ?? '—' }}</td>
                                    <td class="py-3 px-4 whitespace-nowrap text-slate-700">{{ $user->email }}</td>

                                    <td class="py-3 px-4 whitespace-nowrap align-middle">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $rolePill }}">
                                            {{ $uRole }}
                                        </span>
                                    </td>

                                    <td class="py-3 px-4 whitespace-nowrap text-slate-700">
                                        {{ $uRole === 'admin_dinas' ? ($dinasName ?? '—') : '—' }}
                                    </td>

                                    <td class="py-3 px-4 whitespace-nowrap">
                                        @if ($uRole === 'intern')
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusPill }}">
                                                {{ $st }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-4 whitespace-nowrap">
                                        @if ($uRole !== 'intern')
                                            <span class="text-slate-400">—</span>
                                        @else
                                            <div class="font-semibold text-slate-900 tabular-nums">
                                                {{ (int) ($user->computed_score ?? 0) }}
                                            </div>
                                            @if (!empty($user->computed_score_subtitle))
                                                <div class="text-xs text-slate-500">{{ $user->computed_score_subtitle }}</div>
                                            @endif
                                        @endif
                                    </td>

                                    <td class="py-3 px-4 whitespace-nowrap text-right">
                                        @if (auth()->id() === $user->id)
                                            <span
                                                class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                                Akun sendiri
                                            </span>
                                        @else
                                            <div class="inline-flex items-center justify-end gap-2">
                                                @if ($uRole === 'intern' && $st === 'aktif')
                                                    <button type="button"
                                                        class="btnCompleteInternship h-9 inline-flex items-center rounded-lg bg-emerald-600 px-3 text-xs font-semibold text-white
                                                               shadow-sm shadow-emerald-900/10 hover:bg-emerald-700 hover:shadow-md hover:shadow-emerald-900/10
                                                               transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-emerald-200"
                                                        data-url="{{ route('admin.users.complete-internship', $user) }}"
                                                        data-name="{{ $user->name }}">
                                                        Selesai Magang
                                                    </button>
                                                @endif

                                                @if ($uRole === 'intern' && $st === 'tamat')
                                                    <a href="{{ route('admin.users.certificate.pdf', $user) }}"
                                                        class="h-9 inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700
                                                               shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5
                                                               transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                                                        Sertifikat
                                                    </a>
                                                @endif

                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                    class="h-9 inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700
                                                           shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5
                                                           transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                                                    Edit
                                                </a>

                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                    onsubmit="return confirm('Hapus user ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="h-9 inline-flex items-center rounded-lg bg-rose-600 px-3 text-xs font-semibold text-white
                                                               shadow-sm shadow-rose-900/10 hover:bg-rose-700 hover:shadow-md hover:shadow-rose-900/10
                                                               transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-rose-200">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-10 text-center text-slate-600">Belum ada user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        </section>
    </div>

    {{-- MODAL: COMPLETE INTERNSHIP --}}
    <div id="completeInternshipModal" class="hidden fixed inset-0 z-50">
        <div class="absolute inset-0 bg-slate-900/60" data-close="1"></div>

        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-xl rounded-2xl border border-slate-200 bg-white shadow-xl overflow-hidden ring-1 ring-slate-200/60">
                <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between bg-gradient-to-b from-white to-slate-50/40">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Selesai Magang</p>
                        <p id="completeInternshipSubtitle" class="mt-0.5 text-xs text-slate-500">Input penilaian akhir.</p>
                    </div>
                    <button type="button"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700
                               shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5
                               transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200"
                        data-close="1">
                        Tutup
                    </button>
                </div>

                <form id="completeInternshipForm" method="POST" action="#" class="p-6 space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @for ($i = 1; $i <= 4; $i++)
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Aspek {{ $i }}</label>
                                <input name="aspect_{{ $i }}" type="number" min="0" max="100" required
                                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                           shadow-sm shadow-slate-900/5
                                           focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                    placeholder="0-100">
                            </div>
                        @endfor

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700">Aspek 5</label>
                            <input name="aspect_5" type="number" min="0" max="100" required
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                       shadow-sm shadow-slate-900/5
                                       focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                placeholder="0-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Nama Penandatangan (opsional)</label>
                            <input name="signatory_name" type="text"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                       shadow-sm shadow-slate-900/5
                                       focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                placeholder="Kepala Dinas">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Jabatan Penandatangan (opsional)</label>
                            <input name="signatory_title" type="text"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                       shadow-sm shadow-slate-900/5
                                       focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                placeholder="Kepala Dinas Kominfo">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                                   shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5
                                   transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200"
                            data-close="1">
                            Batal
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                                   shadow-sm shadow-slate-900/10 hover:bg-slate-800 hover:shadow-md hover:shadow-slate-900/10
                                   transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                            Simpan & Tamatkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT: Modal + Export --}}
    <script>
        (function() {
            const modal = document.getElementById('completeInternshipModal');
            const form = document.getElementById('completeInternshipForm');
            const subtitle = document.getElementById('completeInternshipSubtitle');

            function openModal(url, name) {
                if (!modal || !form) return;
                form.setAttribute('action', url);
                if (subtitle) subtitle.textContent = 'Input penilaian akhir untuk: ' + (name || '');
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                if (!modal) return;
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }

            document.querySelectorAll('.btnCompleteInternship').forEach((btn) => {
                btn.addEventListener('click', () => {
                    openModal(btn.getAttribute('data-url'), btn.getAttribute('data-name'));
                });
            });

            modal?.querySelectorAll('[data-close="1"]').forEach((el) => {
                el.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeModal();
            });

            // ===== Export dropdown =====
            const btn = document.getElementById('btnExportUsers');
            const menu = document.getElementById('menuExportUsers');
            const chevron = document.getElementById('exportUsersChevron');
            const dlFrame = document.getElementById('dlUsersFrame');

            function openMenu() {
                if (!menu) return;
                menu.classList.remove('hidden');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }

            function closeMenu() {
                if (!menu) return;
                menu.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }

            btn?.addEventListener('click', (e) => {
                e.stopPropagation();
                if (!menu) return;
                menu.classList.contains('hidden') ? openMenu() : closeMenu();
            });

            document.addEventListener('click', closeMenu);
            menu?.addEventListener('click', (e) => e.stopPropagation());

            document.querySelectorAll('.export-users-action').forEach(el => {
                el.addEventListener('click', () => {
                    const url = el.getAttribute('data-url');
                    const label = el.getAttribute('data-label') || 'Export';

                    const original = el.innerHTML;
                    el.disabled = true;
                    el.innerHTML = `
                        <span class="inline-flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full border-2 border-slate-300 border-t-slate-700 animate-spin"></span>
                            <span>Mengekspor ${label}...</span>
                        </span>
                        <span class="text-xs text-slate-400">harap tunggu</span>
                    `;

                    closeMenu();
                    if (url && dlFrame) dlFrame.src = url;

                    setTimeout(() => {
                        el.disabled = false;
                        el.innerHTML = original;
                    }, 1800);
                });
            });
        })();
    </script>

@endsection
