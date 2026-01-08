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

<<<<<<< HEAD
<body x-data="{ sidebarOpen: false }" class="font-sans antialiased bg-slate-50 text-slate-900">
    @php $user = Auth::user(); @endphp
=======
<body x-data="{ sidebarOpen: false }" class="font-sans antialiased bg-slate-900 text-white overflow-x-hidden">
@php $user = Auth::user(); @endphp
>>>>>>> Alek-branch

    <div class="min-h-screen flex">

<<<<<<< HEAD
        {{-- SIDEBAR (off-canvas on mobile, static on desktop) --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-[280px]
               bg-slate-950 text-white border-r border-white/10
               transform transition-transform duration-300 ease-in-out
               lg:static lg:translate-x-0 lg:flex lg:flex-col">
            {{-- Header brand + close button (mobile) --}}
            <div class="h-16 px-6 flex items-center gap-3 border-b border-white/10 relative">
                <div
                    class="h-10 w-10 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('img/logo kab.mgl.png') }}" alt="Logo" class="h-7 w-7 object-contain">
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-semibold">Diskominfo</p>
                    <p class="text-xs text-white/60">Kabupaten Magelang</p>
                </div>

                {{-- Close (mobile) --}}
                <button @click="sidebarOpen = false"
                    class="lg:hidden absolute right-4 top-1/2 -translate-y-1/2 h-9 w-9 rounded-xl
                       bg-white/5 border border-white/10 text-white/70 hover:text-white hover:bg-white/10 transition"
                    aria-label="Tutup sidebar" type="button">
                    ✕
                </button>
            </div>

            <nav class="px-4 py-5 space-y-2">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition
                      {{ request()->routeIs('dashboard') ? 'bg-white/10 border border-white/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <span class="text-base">🏠</span> Dashboard
                </a>

                <a href="{{ route('kiosk.display') }}"
                    class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition
                      {{ request()->routeIs('kiosk.*') || request()->is('/') ? 'bg-white/10 border border-white/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <span class="text-base">🖥️</span> Home / Kiosk
                </a>

                <div class="pt-4">
                    <p class="px-4 text-[11px] uppercase tracking-wider text-white/40">Menu</p>
                    <div class="mt-2 space-y-2">
                        <a href="{{ route('admin.attendance.index') }}"
                            class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold transition
                              {{ request()->routeIs('admin.attendance.*') ? 'bg-white/10 border border-white/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <span class="flex items-center gap-3"><span class="text-base">📌</span> Presensi</span>
                            <span class="text-[11px] text-white/45">Log</span>
                        </a>

                        <a href="{{ route('admin.guest.index') }}"
                            class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold transition
                              {{ request()->routeIs('admin.guest.*') ? 'bg-white/10 border border-white/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <span class="flex items-center gap-3"><span class="text-base">📝</span> Buku Tamu</span>
                            <span class="text-[11px] text-white/45">Log</span>
                        </a>

                        <a href="{{ route('admin.survey.index') }}"
                            class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold transition
                              {{ request()->routeIs('admin.survey.*') ? 'bg-white/10 border border-white/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <span class="flex items-center gap-3"><span class="text-base">⭐</span> Survey</span>
                            <span class="text-[11px] text-white/45">Hasil</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold transition
                              {{ request()->routeIs('admin.users.*') ? 'bg-white/10 border border-white/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <span class="flex items-center gap-3"><span class="text-base">👥</span> Users</span>
                            <span class="text-[11px] text-white/45">Manage</span>
                        </a>
=======
    {{-- SIDEBAR (off-canvas mobile, static desktop) --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-[280px]
               bg-white/10 backdrop-blur-xl text-white border-r border-white/15
               transform transition-transform duration-300 ease-in-out
               lg:static lg:translate-x-0 lg:flex lg:flex-col
               flex flex-col min-h-screen"
    >
        {{-- Header brand + close button (mobile) --}}
        <div class="h-16 px-6 flex items-center gap-3 border-b border-white/15 relative shrink-0">
            <div class="h-10 w-10 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center overflow-hidden">
                <img src="{{ asset('img/logo kab.mgl.png') }}" alt="Logo" class="h-7 w-7 object-contain">
            </div>

            <div class="leading-tight">
                <p class="text-sm font-semibold">Diskominfo</p>
                <p class="text-xs text-white/60">User Panel</p>
            </div>

            {{-- Close (mobile) --}}
            <button
                @click="sidebarOpen = false"
                class="lg:hidden absolute right-4 top-1/2 -translate-y-1/2 h-9 w-9 rounded-xl
                       bg-white/10 border border-white/15 text-white/70 hover:text-white hover:bg-white/20 transition"
                aria-label="Tutup sidebar"
                type="button"
            >
                ✕
            </button>
        </div>

        @php
            // helper aman kalau route belum ada
            $safeRoute = function (string $name, string $fallback) {
                return \Illuminate\Support\Facades\Route::has($name) ? route($name) : url($fallback);
            };

            // sesuaikan dengan route kamu
            $urlProfile = $safeRoute('profile.edit', '/profile'); // bawaan breeze/jetstream
            $urlScanQr  = $safeRoute('attendance.qr', '/attendance/qr');
            $urlHistory = $safeRoute('intern.attendance.history', '/intern/attendance');

            // active by route name (kalau ada) + fallback by path
            $isActiveRoute = function(string $routeName) {
                return request()->routeIs($routeName);
            };
            $isActivePath = function(string $pathStartsWith) {
                return request()->is(ltrim($pathStartsWith, '/').'*');
            };
        @endphp

        {{-- Menu (scrollable) --}}
        <nav class="px-4 py-5 space-y-2 overflow-y-auto grow">
            <p class="px-4 text-[11px] uppercase tracking-wider text-white/40">Menu</p>

            <a href="{{ $urlProfile }}"
               class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition
                      {{ ($isActiveRoute('profile.edit') || $isActivePath('profile')) ? 'bg-white/20 border border-white/20' : 'text-white/80 hover:bg-white/15 hover:text-white' }}">
                <span class="text-base">👤</span> Profil
            </a>

            <a href="{{ $urlScanQr }}"
               class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition
                      {{ ($isActiveRoute('attendance.qr') || $isActivePath('attendance')) ? 'bg-white/20 border border-white/20' : 'text-white/80 hover:bg-white/15 hover:text-white' }}">
                <span class="text-base">📷</span> Scan QR Presensi
            </a>

            <a href="{{ $urlHistory }}"
               class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition
                      {{ ($isActiveRoute('intern.attendance.history') || $isActivePath('intern/attendance')) ? 'bg-white/20 border border-white/20' : 'text-white/80 hover:bg-white/15 hover:text-white' }}">
                <span class="text-base">📌</span> Riwayat Presensi
            </a>
        </nav>

        {{-- Footer (tidak kepotong: mt-auto + shrink-0) --}}
        <div class="mt-auto px-4 pb-5 pt-4 border-t border-white/15 shrink-0">
            <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                <p class="text-xs text-white/60">Login sebagai</p>
                <p class="mt-1 text-sm font-semibold truncate">{{ $user->name }}</p>
                <p class="text-xs text-white/60 truncate">{{ $user->email }}</p>

                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-xl bg-white/10 border border-white/15 px-4 py-2 text-sm font-semibold
                                   hover:bg-white/20 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- OVERLAY (mobile) --}}
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/50 lg:hidden"
        style="display:none;"
        aria-hidden="true"
    ></div>

    {{-- MAIN --}}
    <div class="flex-1 min-w-0 flex flex-col">

        {{-- TOPBAR (glass, admin-like structure) --}}
        <header class="h-16 bg-white/10 backdrop-blur-xl border-b border-white/15 px-4 sm:px-6 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                {{-- Hamburger (mobile) --}}
                <button
                    @click="sidebarOpen = true"
                    class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-xl
                           bg-white/10 border border-white/15 hover:bg-white/20 transition"
                    type="button"
                    aria-label="Buka sidebar"
                >
                    ☰
                </button>

                <div class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate">@yield('page_title', 'User Panel')</p>
                    <p class="text-xs text-white/60 truncate">Diskominfo Kabupaten Magelang</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2">
                    <span class="text-xs text-white/70">📅</span>
                    <span class="text-xs font-semibold text-white/90">{{ now()->format('D, d M Y') }}</span>
                </div>

                <span class="hidden sm:inline-flex items-center rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-200 border border-emerald-300/20">
                    ● Aktif
                </span>

                <div class="flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/15 text-white font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                    <div class="hidden sm:block leading-tight">
                        <p class="text-xs font-semibold text-white">{{ $user->name }}</p>
                        <p class="text-[11px] text-white/60">{{ $user->email }}</p>
>>>>>>> Alek-branch
                    </div>
                </div>
            </nav>

            <div class="mt-auto px-4 pb-5">
                <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                    <p class="text-xs text-white/60">Login sebagai</p>
                    <p class="mt-1 text-sm font-semibold">{{ $user->name }}</p>
                    <p class="text-xs text-white/60">{{ $user->email }}</p>

                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit"
                            class="w-full rounded-xl bg-white/10 border border-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

<<<<<<< HEAD
        {{-- OVERLAY (mobile) --}}
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/40 lg:hidden" style="display:none;" aria-hidden="true"></div>
=======
        {{-- CONTENT (biar gak kepotong & gak bikin scroll horizontal) --}}
        <main class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-6">
            @yield('content')
        </main>
>>>>>>> Alek-branch

        {{-- MAIN --}}
        <div class="flex-1 min-w-0">

            {{-- TOPBAR --}}
            <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    {{-- Hamburger (mobile) --}}
                    <button @click="sidebarOpen = true"
                        class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition"
                        type="button" aria-label="Buka sidebar">
                        ☰
                    </button>

                    <div class="hidden sm:block">
                        <p class="text-sm font-semibold text-slate-900">@yield('page_title', 'Dashboard Admin')</p>
                        <p class="text-xs text-slate-500">Diskominfo Kabupaten Magelang</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="hidden md:flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                        <span class="text-xs text-slate-500">📅</span>
                        <span class="text-xs font-semibold text-slate-700">{{ now()->format('D, d M Y') }}</span>
                    </div>

                    <a href="{{ route('kiosk.display') }}" target="_blank" rel="noopener"
                        class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                        🖥️ Buka Mode Kiosk
                    </a>

                    <span
                        class="hidden sm:inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                        ● Aktif
                    </span>

                    <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2">
                        <span
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-700 font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                        <div class="hidden sm:block leading-tight">
                            <p class="text-xs font-semibold text-slate-900">{{ $user->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="p-4 sm:p-6 space-y-6">
                @yield('content')
            </main>

        </div>
    </div>

</body>

</html>
