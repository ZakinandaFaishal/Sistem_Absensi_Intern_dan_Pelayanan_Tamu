@extends('layouts.admin')

@section('title', 'Survey Pelayanan - Diskominfo Kab. Magelang')
@section('page_title', 'Survey Pelayanan')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Survey Pelayanan</h2>
            <p class="mt-1 text-sm text-slate-600">Rekap penilaian dan masukan dari tamu.</p>
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

        {{-- CARD --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">⭐</span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Daftar Survey</p>
                        <p class="text-xs text-slate-500">Menampilkan data terbaru (paginasi).</p>
                    </div>
                </div>

                <div class="text-xs text-slate-500">
                    Total halaman: <span class="font-semibold text-slate-700">{{ $surveys->lastPage() }}</span>
                    • Total data: <span class="font-semibold text-slate-700">{{ $surveys->total() }}</span>
                </div>
            </div>

            <div class="p-6 space-y-3">
                @forelse($surveys as $survey)
                    <div class="border border-slate-200 rounded-2xl p-4 hover:bg-slate-50/60 transition">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="font-semibold text-slate-900 truncate">
                                        {{ $survey->visit?->name ?? 'Tamu' }}
                                    </div>

                                    <span class="text-slate-400">•</span>

                                    {{-- Rating badge --}}
                                    @php($rating = (int) ($survey->rating ?? 0))
                                    @php($badge = $rating >= 4 ? 'bg-emerald-100 text-emerald-800' : ($rating === 3 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800'))
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">
                                        Rating: {{ $survey->rating ?? '-' }}/5
                                    </span>
                                </div>

                                <div class="mt-1 text-sm text-slate-600">
                                    {{ $survey->visit?->purpose ?? '-' }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    Dikirim:
                                    <span class="font-semibold text-slate-700">
                                        {{ $survey->submitted_at?->format('d M Y H:i') ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="shrink-0">
                                @if ($survey->visit)
                                    <a href="{{ route('guest.survey.show', $survey->visit) }}"
                                        class="inline-flex items-center rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700
                                          hover:bg-slate-200 transition">
                                        Lihat
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if ($survey->comment)
                            <div class="mt-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800">
                                {{ $survey->comment }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-600">
                        Belum ada survey masuk.
                    </div>
                @endforelse

                <div class="pt-3">
                    {{ $surveys->links() }}
                </div>
            </div>
        </section>

    </div>

@endsection
