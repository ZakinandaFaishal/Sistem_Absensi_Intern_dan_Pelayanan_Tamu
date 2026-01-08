@extends('layouts.admin')

@section('title', 'Log Buku Tamu - Diskominfo Kab. Magelang')
@section('page_title', 'Log Buku Tamu')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Log Buku Tamu</h2>
            <p class="mt-1 text-sm text-slate-600">Rekap kunjungan tamu dan penyelesaian layanan.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                  hover:bg-slate-50 transition">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="pt-5 space-y-6">

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        {{-- CARD --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">📝</span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Daftar Kunjungan</p>
                        <p class="text-xs text-slate-500">Menampilkan data terbaru.</p>
                    </div>
                </div>

                <div class="text-xs text-slate-500">
                    Total data: <span class="font-semibold text-slate-700">{{ $visits->count() }}</span>
                </div>
            </div>

            <div class="p-6 space-y-3">
                @forelse($visits as $visit)
                    <div
                        class="border border-slate-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-50/60 transition">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <div class="font-semibold text-slate-900 truncate">{{ $visit->name }}</div>

                                @if ($visit->completed_at)
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        Selesai
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                        Menunggu
                                    </span>
                                @endif
                            </div>

                            <div class="mt-1 text-sm text-slate-600">
                                {{ $visit->purpose }}
                            </div>

                            <div class="mt-1 text-xs text-slate-500">
                                Datang: <span class="font-semibold text-slate-700">{{ $visit->arrived_at }}</span>
                                @if ($visit->completed_at)
                                    • Selesai: <span class="font-semibold text-slate-700">{{ $visit->completed_at }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if ($visit->completed_at)
                                <span
                                    class="inline-flex items-center rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                                    ✓ Selesai
                                </span>
                            @else
                                <form method="POST" action="{{ route('admin.guest.complete', $visit) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                                           hover:bg-slate-800 transition">
                                        Tandai Selesai
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-600">
                        Belum ada data kunjungan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection
