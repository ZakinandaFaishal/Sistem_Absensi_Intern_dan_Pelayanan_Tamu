<nav x-data="{ open: false }" class="sticky top-0 z-50">
    {{-- bar gradient + glass --}}
    <div class="border-b border-white/20 bg-white/35 backdrop-blur-xl">
        {{-- glow tipis di atas --}}
        <div class="pointer-events-none absolute inset-x-0 top-0 h-16">
            <div class="absolute -top-10 left-10 h-28 w-28 rounded-full bg-fuchsia-200/40 blur-2xl"></div>
            <div class="absolute -top-10 right-10 h-28 w-28 rounded-full bg-sky-200/40 blur-2xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                <div class="flex">
                    <!-- Brand -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                            <div class="rounded-2xl bg-white/40 border border-white/40 shadow-sm backdrop-blur p-2">
                                <img
                                    src="{{ asset('img/logo kab.mgl.png') }}"
                                    alt="Logo Kabupaten Magelang"
                                    class="h-8 w-8 object-contain"
                                >
                            </div>

                            <div class="hidden sm:block leading-tight">
                                <div class="text-sm font-extrabold tracking-tight text-slate-900">
                                    Diskominfo
                                </div>
                                <div class="text-xs text-slate-600">
                                    Kabupaten Magelang
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden sm:ms-8 sm:flex items-center gap-2">
                        <x-nav-link
                            :href="route('dashboard')"
                            :active="request()->routeIs('dashboard')"
                            class="rounded-xl px-4 py-2 text-sm font-semibold"
                        >
                            Dashboard
                        </x-nav-link>

                        <x-nav-link
                            :href="route('kiosk.index')"
                            :active="request()->routeIs('kiosk.*') || request()->is('/')"
                            class="rounded-xl px-4 py-2 text-sm font-semibold"
                        >
                            Home
                        </x-nav-link>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                    <!-- Settings Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center gap-2 rounded-2xl bg-white/40 px-3 py-2
                                       border border-white/40 text-sm font-semibold text-slate-700
                                       hover:bg-white/55 transition focus:outline-none shadow-sm"
                            >
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900/5 text-slate-700 ring-1 ring-inset ring-white/30">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>

                                <div class="hidden md:block text-left leading-tight">
                                    <div class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-slate-600">{{ Auth::user()->email }}</div>
                                </div>

                                <svg class="ms-1 h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                Profil
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link
                                    :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                >
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button
                        @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-2xl
                               text-slate-600 hover:text-slate-800 hover:bg-white/40
                               focus:outline-none transition"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-b border-white/20 bg-white/45 backdrop-blur-xl">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('kiosk.index')" :active="request()->routeIs('kiosk.*') || request()->is('/')">
                Home
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-white/20">
            <div class="px-4">
                <div class="font-semibold text-base text-slate-900">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-600">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1 px-4 pb-2">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link
                        :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                    >
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
