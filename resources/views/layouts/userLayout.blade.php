<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'User Panel - Diskominfo Kab. Magelang')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    x-data="{ sidebarOpen: false }"
    @keydown.escape.window="sidebarOpen = false"
    class="font-sans bg-slate-900 text-white h-[100dvh] overflow-hidden"
>
@php
    $user = Auth::user();

    $safeRoute = function (string $name, string $fallback) {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : url($fallback);
    };

    $urlProfile = $safeRoute('intern.userProfile', '/intern/userProfile');
    $urlScanQr  = $safeRoute('attendance.qr', '/presensi/scan-qr');
    $urlHistory = $safeRoute('intern.attendance.history', '/intern/presensi');

    $activeProfile = request()->routeIs('intern.userProfile') || request()->is('intern/userProfile');
    $activeScanQr  = request()->routeIs('attendance.qr')
        || request()->routeIs('attendance.scan.*')
        || request()->is('presensi/scan*');
    $activeHistory = request()->routeIs('intern.attendance.history') || request()->is('intern/presensi*');

    $navItem = function (bool $active) {
        return 'group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm transition ' .
            ($active
                ? 'bg-white/20 text-white font-semibold ring-1 ring-white/15'
                : 'text-white/70 hover:text-white hover:bg-white/10');
    };
@endphp

<div class="h-[100dvh] w-full overflow-hidden">

    {{-- SIDEBAR --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'"
        class="fixed left-0 top-0 z-50 h-[100dvh] w-72 sm:w-64
               bg-white/10 backdrop-blur-xl border-r border-white/15
               transform transition-transform duration-300 ease-out"
        aria-label="Sidebar User"
    >
        <div class="flex h-full flex-col">

            {{-- Brand --}}
            <div class="relative h-16 shrink-0 px-5 flex items-center gap-3 border-b border-white/15">
                <div class="h-9 w-9 rounded-2xl bg-white/20 border border-white/10 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('img/logo_kab_mgl.png') }}" class="h-6 w-6 object-contain" alt="Logo">
                </div>

                <div class="leading-tight min-w-0">
                    <p class="text-sm font-extrabold truncate">Diskominfo</p>
                    <p class="text-xs text-white/60 truncate">User Panel</p>
                </div>

                {{-- Close (mobile) --}}
                <button
                    type="button"
                    @click="sidebarOpen = false"
                    class="sm:hidden absolute right-4 top-1/2 -translate-y-1/2 h-9 w-9 rounded-2xl
                           bg-white/10 hover:bg-white/20 border border-white/10 transition
                           inline-flex items-center justify-center"
                    aria-label="Tutup sidebar"
                >
                    <x-icon name="x-mark" class="h-6 w-6" />
                </button>
            </div>

            {{-- Menu --}}
            <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-2">
                <a href="{{ $urlProfile }}" class="{{ $navItem($activeProfile) }}">
                    <span class="h-9 w-9 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center transition group-hover:bg-white/15">
                        <x-icon name="user" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0">
                        <p class="leading-tight truncate">Profil</p>
                        <p class="text-[11px] text-white/50 leading-tight">Data akun & pengaturan</p>
                    </div>
                </a>

                <a href="{{ $urlScanQr }}" class="{{ $navItem($activeScanQr) }}">
                    <span class="h-9 w-9 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center transition group-hover:bg-white/15">
                        <x-icon name="camera" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0">
                        <p class="leading-tight truncate">Scan QR Presensi</p>
                        <p class="text-[11px] text-white/50 leading-tight">Check-in / check-out</p>
                    </div>
                </a>

                <a href="{{ $urlHistory }}" class="{{ $navItem($activeHistory) }}">
                    <span class="h-9 w-9 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center transition group-hover:bg-white/15">
                        <x-icon name="map-pin" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0">
                        <p class="leading-tight truncate">Riwayat Presensi</p>
                        <p class="text-[11px] text-white/50 leading-tight">Rekap kehadiran</p>
                    </div>
                </a>
            </nav>

            {{-- Footer --}}
            <div class="shrink-0 p-4 border-t border-white/15 pb-[calc(env(safe-area-inset-bottom)+1rem)]">
                <div class="rounded-2xl bg-white/10 border border-white/10 px-4 py-3">
                    <p class="text-[11px] uppercase tracking-wider text-white/45">Login sebagai</p>
                    <p class="mt-1 text-sm font-semibold truncate">{{ $user->name }}</p>
                    <p class="text-xs text-white/60 truncate">{{ $user->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10
                               px-4 py-2.5 text-sm font-semibold transition
                               inline-flex items-center justify-center"
                    >
                        Logout
                    </button>
                </form>
            </div>

        </div>
    </aside>

    {{-- Backdrop (mobile) --}}
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/40 sm:hidden"
        style="display:none;"
        aria-hidden="true"
    ></div>

    {{-- MAIN --}}
    <div class="h-[100dvh] min-h-0 overflow-hidden flex flex-col sm:pl-64">

        {{-- TOPBAR --}}
        <header class="h-16 shrink-0 flex items-center justify-between px-4 sm:px-6
                       bg-white/10 backdrop-blur-xl border-b border-white/15">
            <div class="flex items-center gap-3 min-w-0">
                <button
                    type="button"
                    @click="sidebarOpen = true"
                    class="sm:hidden inline-flex items-center justify-center h-9 w-9 rounded-2xl
                           bg-white/10 hover:bg-white/20 border border-white/10 transition"
                    aria-label="Buka sidebar"
                >
                    <x-icon name="bars-3" class="h-6 w-6" />
                </button>

                <div class="min-w-0">
                    <h1 class="text-sm sm:text-base font-extrabold tracking-tight truncate">
                        @yield('page_title', 'User Panel')
                    </h1>
                    <p class="text-[11px] text-white/60 truncate">
                        @yield('page_subtitle', 'Diskominfo Kabupaten Magelang')
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-2 rounded-2xl border border-white/15 bg-white/10 px-3 py-2">
                    <x-icon name="calendar-days" class="h-4 w-4 text-white/70" />
                    <span class="text-xs font-semibold text-white/90">{{ now()->format('D, d M Y') }}</span>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-6 pb-[calc(env(safe-area-inset-bottom)+1rem)]">
            @yield('content')
        </main>

    </div>
</div>

</body>
</html>
