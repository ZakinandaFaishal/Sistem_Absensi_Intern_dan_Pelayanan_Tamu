<x-guest-layout>
    <div class="space-y-8">
        <div class="text-center">
            <div
                class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-semibold text-gray-700">
                Portal Layanan
            </div>
            <h1 class="mt-4 text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">
                <span class="block">SIMANTA</span>
                <span class="block mt-1 text-base sm:text-lg font-semibold text-gray-700">Sistem Informasi Manajemen Magang &amp; Tamu</span>
            </h1>
            <p class="mt-2 text-sm sm:text-base text-gray-600">
                Absensi internal dan pelayanan tamu dalam satu sistem.
            </p>
        </div>

        <div class="grid gap-4 sm:gap-6 sm:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur p-5 shadow-sm">
                <div class="text-sm font-semibold text-gray-900">Kiosk</div>
                <div class="mt-1 text-sm text-gray-600">Untuk Admin/Magang (perlu login) — presensi via QR.</div>
                <a href="{{ route('kiosk.index') }}"
                    class="mt-4 w-full inline-flex justify-center items-center px-4 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-sm text-white tracking-wide hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Buka Portal Presensi
                </a>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur p-5 shadow-sm">
                <div class="text-sm font-semibold text-gray-900">Buku Tamu</div>
                <div class="mt-1 text-sm text-gray-600">Isi data kunjungan tamu dengan cepat dan rapi.</div>
                <a href="{{ route('guest.create') }}"
                    class="mt-4 w-full inline-flex justify-center items-center px-4 py-3 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-800 tracking-wide hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Isi Buku Tamu
                </a>
            </div>
        </div>

        <div class="flex justify-center">
            @auth
                <a href="{{ route('dashboard') }}"
                    class="inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-800 tracking-wide hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Masuk ke Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-800 tracking-wide hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Login Admin/Magang
                </a>
            @endauth
        </div>
    </div>
</x-guest-layout>
