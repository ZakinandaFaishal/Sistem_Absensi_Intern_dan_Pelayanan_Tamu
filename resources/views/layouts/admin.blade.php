<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ 'SIMANTA' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false"
    class="font-sans antialiased bg-slate-50 text-slate-900 overflow-hidden">

    @php
        $user = Auth::user();
        $user?->loadMissing('dinas');
    @endphp

    {{-- pakai dvh biar tidak “kepotong” di mobile browser yang punya bottom bar --}}
    <div class="h-[100dvh] overflow-hidden">

        {{-- SIDEBAR --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed left-0 top-0 z-50 h-[100dvh] w-[280px]
               bg-slate-950 text-white border-r border-white/10
               transform transition-transform duration-300 ease-in-out"
            aria-label="Sidebar Admin">

            <div class="flex h-full flex-col">

                {{-- Brand --}}
                <div class="relative flex items-center gap-3 px-4 py-5 border-b border-white/10">
                    <div
                        class="h-12 w-12 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center overflow-hidden shrink-0">
                        <img src="{{ asset('img/logo_kab_mgl.png') }}" alt="Logo" class="h-8 w-8 object-contain">
                    </div>

                    <div class="min-w-0 leading-snug">
                        <p class="text-sm font-semibold leading-snug">SIMANTA</p>
                        <p class="mt-0.5 text-xs text-white/70 leading-snug whitespace-normal">
                            Sistem Informasi Manajemen Magang &amp; Tamu
                        </p>
                        <p class="mt-0.5 text-xs text-white/60 truncate">
                            {{ $user && ($user->role ?? null) === 'admin_dinas' ? $user->dinas?->name ?? '—' : 'Kabupaten Magelang' }}
                        </p>
                    </div>

                    {{-- Close (mobile) --}}
                    <button type="button" @click="sidebarOpen = false"
                        class="md:hidden absolute right-4 top-1/2 -translate-y-1/2 h-9 w-9 rounded-xl
                           bg-white/5 border border-white/10 text-white/70 hover:text-white hover:bg-white/10 transition
                           inline-flex items-center justify-center"
                        aria-label="Tutup sidebar">
                        <x-icon name="x-mark" class="h-6 w-6" />
                    </button>
                </div>

                {{-- Menu --}}
                <nav
                    class="flex-1 overflow-y-auto px-4 py-5 space-y-5
                            pb-[calc(1.25rem+env(safe-area-inset-bottom))]">

                    {{-- Date (sidebar) --}}
                    <div
                        class="hidden md:flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-3 py-3">
                        <x-icon name="calendar-days" class="h-4 w-4 text-white/70" />
                        <span class="text-xs font-semibold text-white/80">
                            {{ now()->format('D, d M Y') }}
                        </span>
                    </div>

                    {{-- Quick actions --}}
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('admin.dashboard') }}"
                            class="rounded-2xl border border-white/10 bg-white/5 px-3 py-3 text-xs font-semibold
                               hover:bg-white/10 transition flex items-center gap-2">
                            <x-icon name="home" class="h-5 w-5" />
                            Dashboard
                        </a>

                        <a href="{{ route('kiosk.display') }}"
                            class="rounded-2xl border border-white/10 bg-white/5 px-3 py-3 text-xs font-semibold
                               hover:bg-white/10 transition flex items-center gap-2">
                            <x-icon name="computer-desktop" class="h-5 w-5" />
                            Main Page
                        </a>
                    </div>

                    {{-- Section title --}}
                    <div>
                        <p class="px-1 text-[11px] uppercase tracking-wider text-white/40">Menu</p>

                        <div class="mt-2 space-y-2">

                            {{-- PRESENSI --}}
                            <div x-data="{ open: {{ request()->routeIs('admin.attendance.*') ? 'true' : 'false' }} }"
                                class="rounded-2xl border border-white/10 bg-white/5 overflow-hidden">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold transition
                                    {{ request()->routeIs('admin.attendance.*') ? 'bg-white/10 text-white' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                                    <span class="flex items-center gap-3">
                                        <x-icon name="map-pin" class="h-5 w-5" />
                                        Presensi
                                    </span>

                                    <span class="inline-flex items-center gap-2 text-[11px] text-white/55">
                                        <span>Menu</span>
                                        <span class="transition"
                                            :style="open ? 'transform: rotate(180deg)' : 'transform: rotate(0deg)'">▾</span>
                                    </span>
                                </button>

                                <div x-show="open" x-transition.opacity class="px-2 pb-2" style="display:none;">
                                    @if (($user->role ?? null) === 'super_admin')
                                        <a href="{{ route('admin.attendance.index') }}"
                                            class="mt-1 flex items-center justify-between rounded-xl px-3 py-2 text-xs font-semibold transition
                                       {{ request()->routeIs('admin.attendance.index') ? 'bg-white/10 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                                            <span>Daftar Presensi</span>
                                            <span class="text-white/45">/admin/presensi</span>
                                        </a>
                                    @endif

                                    <div class="mt-2 grid grid-cols-1 gap-2">
                                        <a href="{{ route('admin.attendance.manage') }}"
                                            class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-[11px] font-semibold text-white/80 hover:bg-white/10 hover:text-white transition">
                                            Pengaturan Presensi
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- BUKU TAMU --}}
                            <div x-data="{ open: {{ request()->routeIs('admin.guest.*') ? 'true' : 'false' }} }"
                                class="rounded-2xl border border-white/10 bg-white/5 overflow-hidden">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold transition
                                    {{ request()->routeIs('admin.guest.*') ? 'bg-white/10 text-white' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                                    <span class="flex items-center gap-3">
                                        <x-icon name="book-open" class="h-5 w-5" />
                                        Buku Tamu
                                    </span>

                                    <span class="inline-flex items-center gap-2 text-[11px] text-white/55">
                                        <span>Menu</span>
                                        <span class="transition"
                                            :style="open ? 'transform: rotate(180deg)' : 'transform: rotate(0deg)'">▾</span>
                                    </span>
                                </button>

                                <div x-show="open" x-transition.opacity class="px-2 pb-2" style="display:none;">
                                    <a href="{{ route('admin.guest.index') }}"
                                        class="mt-1 flex items-center justify-between rounded-xl px-3 py-2 text-xs font-semibold transition
                                   {{ request()->routeIs('admin.guest.index') ? 'bg-white/10 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                                        <span>Log Buku Tamu</span>
                                        <span class="text-white/45">/admin/tamu</span>
                                    </a>
                                </div>
                            </div>

                            {{-- SURVEY --}}
                            <div x-data="{ open: {{ request()->routeIs('admin.survey.*') ? 'true' : 'false' }} }"
                                class="rounded-2xl border border-white/10 bg-white/5 overflow-hidden">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold transition
                                    {{ request()->routeIs('admin.survey.*') ? 'bg-white/10 text-white' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                                    <span class="flex items-center gap-3">
                                        <x-icon name="star" class="h-5 w-5" />
                                        Survey
                                    </span>

                                    <span class="inline-flex items-center gap-2 text-[11px] text-white/55">
                                        <span>Menu</span>
                                        <span class="transition"
                                            :style="open ? 'transform: rotate(180deg)' : 'transform: rotate(0deg)'">▾</span>
                                    </span>
                                </button>

                                <div x-show="open" x-transition.opacity class="px-2 pb-2" style="display:none;">
                                    <a href="{{ route('admin.survey.index') }}"
                                        class="mt-1 flex items-center justify-between rounded-xl px-3 py-2 text-xs font-semibold transition
                                   {{ request()->routeIs('admin.survey.index') ? 'bg-white/10 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                                        <span>Daftar Survey</span>
                                        <span class="text-white/45">/admin/survey</span>
                                    </a>
                                </div>
                            </div>

                            {{-- USERS --}}
                            <div x-data="{ open: {{ request()->routeIs('admin.users.*') ? 'true' : 'false' }} }"
                                class="rounded-2xl border border-white/10 bg-white/5 overflow-hidden">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold transition
                                    {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                                    <span class="flex items-center gap-3">
                                        <x-icon name="users" class="h-5 w-5" />
                                        Users
                                    </span>

                                    <span class="inline-flex items-center gap-2 text-[11px] text-white/55">
                                        <span>Menu</span>
                                        <span class="transition"
                                            :style="open ? 'transform: rotate(180deg)' : 'transform: rotate(0deg)'">▾</span>
                                    </span>
                                </button>

                                <div x-show="open" x-transition.opacity class="px-2 pb-2" style="display:none;">
                                    <a href="{{ route('admin.users.index') }}"
                                        class="mt-1 flex items-center justify-between rounded-xl px-3 py-2 text-xs font-semibold transition
                                   {{ request()->routeIs('admin.users.index') ? 'bg-white/10 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                                        <span>Manajemen Users</span>
                                        <span class="text-white/45">/admin/users</span>
                                    </a>

                                    <div class="mt-2 grid grid-cols-1 gap-2">
                                        <a href="{{ route('admin.users.create') }}"
                                            class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-[11px] font-semibold text-white/80 hover:bg-white/10 hover:text-white transition">
                                            Tambah User
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </nav>

                {{-- Sidebar footer --}}
                <div class="shrink-0 px-4 pb-[calc(1.25rem+env(safe-area-inset-bottom))]">
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                        <p class="text-xs text-white/60">Login sebagai</p>
                        <p class="mt-1 text-sm font-semibold">{{ $user->name }}</p>
                        <p class="text-xs text-white/60 truncate">{{ $user->email }}</p>

                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf
                            <button type="submit"
                                class="w-full rounded-xl bg-white/10 border border-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15 transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </aside>

        {{-- OVERLAY (mobile) --}}
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/40 md:hidden" style="display:none;" aria-hidden="true"></div>

        {{-- MOBILE: hamburger kiri atas (safe-area top) --}}
        <button type="button" @click="sidebarOpen = true" x-show="!sidebarOpen" x-transition.opacity
            class="md:hidden fixed left-4 z-40 inline-flex items-center justify-center
            h-11 w-11 rounded-2xl border border-slate-200 bg-white/95 backdrop-blur
            shadow-lg hover:bg-slate-50 active:scale-[0.98] transition
            top-[calc(1rem+env(safe-area-inset-top))]"
            aria-label="Buka menu">
            <x-icon name="bars-3" class="h-6 w-6 text-slate-700" />
        </button>

        {{-- MAIN --}}
        <div class="h-[100dvh] flex flex-col md:pl-[280px]">
            {{-- padding bawah pakai safe-area agar konten tidak ketutup bottom bar --}}
            <main id="adminMainScroll"
                class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6
                       pt-16 md:pt-6
                       pb-[calc(1.5rem+env(safe-area-inset-bottom))]">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const main = document.getElementById('adminMainScroll');
            if (!main) return;

            const scrollToHash = (hash) => {
                if (!hash || hash === '#') return;

                const id = hash.startsWith('#') ? hash.slice(1) : hash;
                if (!id) return;

                const target = document.getElementById(id);
                if (!target) return;

                const mainRect = main.getBoundingClientRect();
                const targetRect = target.getBoundingClientRect();
                const top = targetRect.top - mainRect.top + main.scrollTop - 12;

                main.scrollTo({
                    top: Math.max(0, top),
                    behavior: 'smooth'
                });
            };

            if (window.location.hash) setTimeout(() => scrollToHash(window.location.hash), 0);

            document.body.addEventListener('click', (e) => {
                const link = e.target?.closest?.('a[href]');
                if (!link) return;

                const href = link.getAttribute('href') || '';
                if (!href.includes('#')) return;

                const url = new URL(link.href, window.location.href);
                if (url.pathname !== window.location.pathname) return;
                if (!url.hash) return;

                e.preventDefault();
                history.pushState(null, '', url.hash);
                scrollToHash(url.hash);
            });

            window.addEventListener('hashchange', () => scrollToHash(window.location.hash));
        });
    </script>

</body>

</html>
