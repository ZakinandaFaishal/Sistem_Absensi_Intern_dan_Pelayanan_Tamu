<x-kiosk-layout title="Kiosk">
    <div class="min-h-[70vh] flex items-center justify-center">
        <div class="w-full max-w-md space-y-6">

            {{-- Judul --}}
            <div class="text-center">
                <h1 class="text-2xl font-extrabold text-slate-900">
                    Layanan Mandiri
                </h1>
                <p class="mt-1 text-sm text-slate-600">
                    Silakan pilih menu yang tersedia
                </p>
            </div>

            {{-- Tombol --}}
            <div class="space-y-4">
                <a
                    href="{{ route('kiosk.absensi') }}"
                    class="group flex w-full items-center justify-center rounded-2xl bg-slate-900 px-6 py-5 text-lg font-bold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300"
                >
                    <span class="mr-2">📌</span>
                    Absensi Magang
                </a>

                <a
                    href="{{ route('guest.create') }}"
                    class="group flex w-full items-center justify-center rounded-2xl bg-emerald-700 px-6 py-5 text-lg font-bold text-white shadow-sm transition hover:bg-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-300"
                >
                    <span class="mr-2">📝</span>
                    Buku Tamu
                </a>
            </div>

            {{-- Catatan --}}
            <div class="rounded-xl bg-slate-50 p-4 text-center text-sm text-slate-600">
                Gunakan menu sesuai keperluan Anda
            </div>

        </div>
    </div>
</x-kiosk-layout>
