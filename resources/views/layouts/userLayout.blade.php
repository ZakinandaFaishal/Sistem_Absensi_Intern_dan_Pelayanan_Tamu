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

<body x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false"
    class="font-sans bg-slate-900 text-white h-[100dvh] overflow-hidden">
    @php $user = Auth::user(); @endphp

    @php
        $safeRoute = function (string $name, string $fallback) {
            return \Illuminate\Support\Facades\Route::has($name) ? route($name) : url($fallback);
        };

        $urlProfile = $safeRoute('intern.userProfile', '/intern/userProfile');
        $urlScanQr = $safeRoute('attendance.qr', '/presensi/scan-qr');
        $urlHistory = $safeRoute('intern.attendance.history', '/intern/presensi');

        $itemClass = function (bool $active) {
            return 'flex items-center gap-3 rounded-xl px-3 py-2 transition ' .
                ($active ? 'bg-white/25 font-semibold text-white' : 'text-white/70 hover:bg-white/15 hover:text-white');
        };

        $activeProfile = request()->routeIs('intern.userProfile') || request()->is('intern/userProfile');
        $activeScanQr =
            request()->routeIs('attendance.qr') ||
            request()->routeIs('attendance.scan.*') ||
            request()->is('presensi/scan*');
        $activeHistory = request()->routeIs('intern.attendance.history') || request()->is('intern/presensi*');
    @endphp

    <div class="h-[100dvh] w-full overflow-hidden">

        {{-- SIDEBAR --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'"
            class="fixed left-0 top-0 z-50 w-72 sm:w-64 h-[100dvh]
               bg-white/10 backdrop-blur-xl border-r border-white/15
               transform transition-transform duration-300 ease-out">
            <div class="flex h-full flex-col">

                {{-- Brand --}}
                <div class="h-16 px-5 flex items-center gap-3 border-b border-white/15 shrink-0 relative">
                    <div class="h-9 w-9 rounded-xl bg-white/20 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('img/logo kab.mgl.png') }}" class="h-6 w-6 object-contain" alt="Logo">
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-bold">Diskominfo</p>
                        <p class="text-xs text-white/60">User Panel</p>
                    </div>

                    {{-- Close button (mobile) --}}
                    <button @click="sidebarOpen = false"
                        class="sm:hidden absolute right-4 top-1/2 -translate-y-1/2 h-9 w-9 rounded-xl
                           bg-white/15 hover:bg-white/25 transition"
                        aria-label="Tutup sidebar" type="button">
                        <x-icon name="x-mark" class="h-6 w-6" />
                    </button>
                </div>

                {{-- Menu --}}
                <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-2 text-sm">
                    <a href="{{ $urlProfile }}" class="{{ $itemClass($activeProfile) }}">
                        <x-icon name="user" class="h-5 w-5" />
                        <span>Profil</span>
                    </a>

                    <a href="{{ $urlScanQr }}" class="{{ $itemClass($activeScanQr) }}">
                        <x-icon name="camera" class="h-5 w-5" />
                        <span>Scan QR Presensi</span>
                    </a>

                    <a href="{{ $urlHistory }}" class="{{ $itemClass($activeHistory) }}">
                        <x-icon name="map-pin" class="h-5 w-5" />
                        <span>Riwayat Presensi</span>
                    </a>
                </nav>

                {{-- Footer --}}
                <div class="shrink-0 p-4 border-t border-white/15 pb-[calc(env(safe-area-inset-bottom)+1rem)]">
                    <div class="rounded-xl bg-white/10 px-3 py-2">
                        <p class="text-xs text-white/60">Login sebagai</p>
                        <p class="text-sm font-semibold truncate">{{ $user->name }}</p>
                        <p class="text-xs text-white/60 truncate">{{ $user->email }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit"
                            class="w-full rounded-xl bg-white/10 hover:bg-white/20 px-3 py-2 text-sm font-semibold transition">
                            Logout
                        </button>
                    </form>
                </div>

            </div>
        </aside>

        {{-- Backdrop mobile --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 z-40 sm:hidden"
            x-transition.opacity style="display:none;" aria-hidden="true"></div>

        {{-- MAIN --}}
        <div class="h-[100dvh] flex flex-col sm:pl-64 min-h-0 overflow-hidden">

            {{-- TOPBAR --}}
            <header
                class="h-16 shrink-0 flex items-center justify-between px-4 sm:px-6
                       bg-white/10 backdrop-blur-xl border-b border-white/15">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = true"
                        class="sm:hidden inline-flex items-center justify-center h-9 w-9
                           rounded-xl bg-white/15 hover:bg-white/25 transition"
                        type="button" aria-label="Buka sidebar">
                        <x-icon name="bars-3" class="h-6 w-6" />
                    </button>

                    <div class="min-w-0">
                        <h1 class="text-sm font-bold truncate">@yield('page_title', 'User Panel')</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="hidden md:flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2">
                        <x-icon name="calendar-days" class="h-4 w-4 text-white/70" />
                        <span class="text-xs font-semibold text-white/90">{{ now()->format('D, d M Y') }}</span>
                    </div>
                </div>
            </header>

            {{-- CONTENT (scrollable) --}}
            <main class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-6 pb-[calc(env(safe-area-inset-bottom)+1rem)]">
                @yield('content')
            </main>

        </div>
    </div>
</body>

</html>
