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

<body class="font-sans bg-slate-900 text-white overflow-x-hidden">
    @php $user = Auth::user(); @endphp

    <div x-data="{ sidebarOpen: false }" class="min-h-screen w-full flex overflow-x-hidden">

        {{-- SIDEBAR --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'"
            class="fixed inset-y-0 left-0 sm:sticky sm:top-0 sm:inset-auto sm:left-auto shrink-0 z-40 w-72 sm:w-64
               bg-white/10 backdrop-blur-xl border-r border-white/15
               transition-transform duration-300 ease-out
               flex flex-col h-screen overflow-hidden">

            {{-- Brand --}}
            <div class="h-16 px-5 flex items-center gap-3 border-b border-white/15 shrink-0">
                <div class="h-9 w-9 rounded-xl bg-white/20 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('img/logo kab.mgl.png') }}" class="h-6 w-6 object-contain" alt="Logo">
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-bold">Diskominfo</p>
                    <p class="text-xs text-white/60">User Panel</p>
                </div>
            </div>

            @php
                $safeRoute = function (string $name, string $fallback) {
                    return \Illuminate\Support\Facades\Route::has($name) ? route($name) : url($fallback);
                };

                $urlProfile = $safeRoute('intern.userProfile', '/intern/userProfile');
                $urlHistory = $safeRoute('intern.attendance.history', '/intern/presensi');
                $urlScanQr = $safeRoute('attendance.qr', '/presensi/scan-qr');

                $isActiveProfile = function () {
                    return request()->routeIs('intern.userProfile') || request()->is('intern/userProfile');
                };
                $isActiveScan = function () {
                    return request()->routeIs('attendance.qr') ||
                        request()->routeIs('attendance.scan.*') ||
                        request()->is('presensi/*');
                };
                $isActiveHistory = function () {
                    return request()->routeIs('intern.attendance.history') || request()->is('intern/presensi*');
                };
            @endphp

            {{-- Menu (scrollable kalau layar pendek) --}}
            <nav class="px-4 py-5 space-y-2 text-sm overflow-y-auto grow min-h-0">
                <a href="{{ $urlProfile }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2 transition
                      {{ $isActiveProfile() ? 'bg-white/25 font-semibold' : 'text-white/70 hover:bg-white/15 hover:text-white' }}">
                    <span class="text-base">👤</span>
                    <span>Profil</span>
                </a>

                <a href="{{ $urlScanQr }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2 transition
                      {{ $isActiveScan() ? 'bg-white/25 font-semibold' : 'text-white/70 hover:bg-white/15 hover:text-white' }}">
                    <span class="text-base">📷</span>
                    <span>Scan QR Presensi</span>
                </a>

                <a href="{{ $urlHistory }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2 transition
                      {{ $isActiveHistory() ? 'bg-white/25 font-semibold' : 'text-white/70 hover:bg-white/15 hover:text-white' }}">
                    <span class="text-base">📌</span>
                    <span>Riwayat Presensi</span>
                </a>
            </nav>

            {{-- Footer sidebar (PAKAI mt-auto, BUKAN absolute) --}}
            <div class="mt-auto p-4 border-t border-white/15 shrink-0">
                <div class="rounded-xl bg-white/10 px-3 py-2">
                    <p class="text-xs text-white/60">Login sebagai</p>
                    <p class="text-sm font-semibold truncate">{{ $user->name }}</p>
                    <p class="text-xs text-white/60 truncate">{{ $user->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-xl bg-white/10 hover:bg-white/20
                               px-3 py-2 text-sm font-semibold transition">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- Backdrop mobile --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 z-30 sm:hidden"
            x-transition.opacity></div>

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">

            {{-- TOPBAR --}}
            <header
                class="h-16 shrink-0 flex items-center justify-between px-4 sm:px-6
                       bg-white/10 backdrop-blur-xl border-b border-white/15">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = true"
                        class="sm:hidden inline-flex items-center justify-center h-9 w-9
                       rounded-xl bg-white/15 hover:bg-white/25 transition"
                        type="button" aria-label="Buka sidebar">
                        ☰
                    </button>

                    <div class="min-w-0">
                        <h1 class="text-sm font-bold truncate">@yield('page_title', 'User Panel')</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="hidden md:flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2">
                        <span class="text-xs text-white/70">📅</span>
                        <span class="text-xs font-semibold text-white/90">{{ now()->format('D, d M Y') }}</span>
                    </div>

                    <div class="flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2">
                        <span
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/15 text-white font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                        <div class="hidden sm:block leading-tight">
                            <p class="text-xs font-semibold text-white">{{ $user->name }}</p>
                            <p class="text-[11px] text-white/60">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            </header>

            {{-- CONTENT (biar gak kepotong & rapi) --}}
            <main class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-6">
                @yield('content')
            </main>

        </div>

    </div>
</body>

</html>
