<x-guest-layout>
    <div class="relative mx-auto max-w-3xl px-4">
        <form
            method="POST"
            action="{{ route('guest.survey.store', $visit) }}"
            class="relative space-y-7 rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-xl shadow-xl sm:p-8"
        >
            @csrf

            <header class="text-center space-y-2">
                <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-2xl border border-white/20 bg-white/15 backdrop-blur shadow-lg">
                    <x-icon name="star" class="h-8 w-8 text-white" />
                </div>

                <h1 class="text-2xl font-extrabold tracking-tight text-white drop-shadow">
                    Survey Kepuasan Pelayanan
                </h1>

                <p class="text-sm text-white/70">
                    Opsional. Pilih jawaban yang paling sesuai.
                </p>

                <div class="mx-auto mt-4 h-[2px] w-24 rounded-full bg-gradient-to-r from-transparent via-white/40 to-transparent"></div>
            </header>

            @php
                $questions = [
                    'q1' => [
                        'title' => 'Bagaimana pendapat Saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?',
                        'options' => [1 => 'Tidak sesuai', 2 => 'Kurang sesuai', 3 => 'Sesuai', 4 => 'Sangat sesuai'],
                    ],
                    'q2' => [
                        'title' => 'Bagaimana pemahaman Saudara tentang kemudahan prosedur pelayanan di unit ini?',
                        'options' => [1 => 'Tidak mudah', 2 => 'Kurang mudah', 3 => 'Mudah', 4 => 'Sangat mudah'],
                    ],
                    'q3' => [
                        'title' => 'Bagaimana pendapat Saudara tentang kecepatan waktu dalam memberikan pelayanan?',
                        'options' => [1 => 'Tidak cepat', 2 => 'Kurang cepat', 3 => 'Cepat', 4 => 'Sangat cepat'],
                    ],
                    'q4' => [
                        'title' => 'Bagaimana pendapat Saudara tentang kewajaran biaya/tarif dalam pelayanan?',
                        'options' => [1 => 'Sangat mahal', 2 => 'Cukup mahal', 3 => 'Murah', 4 => 'Gratis'],
                    ],
                    'q5' => [
                        'title' => 'Bagaimana pendapat Saudara tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?',
                        'options' => [1 => 'Tidak sesuai', 2 => 'Kurang sesuai', 3 => 'Sesuai', 4 => 'Sangat sesuai'],
                    ],
                    'q6' => [
                        'title' => 'Bagaimana pendapat Saudara tentang kompetensi/kemampuan petugas dalam pelayanan?',
                        'options' => [1 => 'Tidak kompeten', 2 => 'Kurang kompeten', 3 => 'Kompeten', 4 => 'Sangat kompeten'],
                    ],
                    'q7' => [
                        'title' => 'Bagaimana pendapat Saudara tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?',
                        'options' => [1 => 'Tidak sopan dan ramah', 2 => 'Kurang sopan dan ramah', 3 => 'Sopan dan ramah', 4 => 'Sangat sopan dan ramah'],
                    ],
                    'q8' => [
                        'title' => 'Bagaimana pendapat Saudara tentang kualitas sarana dan prasarana?',
                        'options' => [1 => 'Buruk', 2 => 'Cukup', 3 => 'Baik', 4 => 'Sangat baik'],
                    ],
                    'q9' => [
                        'title' => 'Bagaimana pendapat Saudara tentang penanganan pengaduan pengguna layanan?',
                        'options' => [1 => 'Tidak ada', 2 => 'Ada tetapi tidak berfungsi', 3 => 'Berfungsi kurang maksimal', 4 => 'Dikelola dengan baik'],
                    ],
                ];
            @endphp

            <section class="space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-white/90">
                            II. Pendapat Responden Tentang Pelayanan
                        </p>
                        <p class="text-xs text-white/60">
                            Pilih salah satu jawaban di setiap pertanyaan.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    @foreach ($questions as $name => $q)
                        <article class="rounded-2xl border border-white/15 bg-white/10 p-4 sm:p-5">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm font-semibold leading-snug text-white/90">
                                    {{ $q['title'] }}
                                </p>

                                <span class="shrink-0 inline-flex items-center rounded-lg border border-white/15 bg-white/10 px-2.5 py-1 text-[11px] font-semibold text-white/75">
                                    {{ strtoupper($name) }}
                                </span>
                            </div>

                            <div class="mt-3 space-y-2">
                                @foreach ($q['options'] as $val => $label)
                                    <label class="group flex items-center gap-3 rounded-xl border border-white/15 bg-white/5 px-4 py-3 transition hover:bg-white/10">
                                        <input
                                            type="radio"
                                            name="{{ $name }}"
                                            value="{{ $val }}"
                                            class="h-4 w-4 border-white/30 bg-white/10 text-white focus:ring-white/40"
                                            {{ old($name) == (string) $val ? 'checked' : '' }}
                                        />
                                        <span class="text-sm text-white/85">
                                            {{ $label }}
                                        </span>
                                    </label>
                                @endforeach

                                <x-input-error class="mt-2 text-red-200" :messages="$errors->get($name)" />
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="space-y-1">
                <label for="comment" class="text-sm font-semibold text-white/90">
                    Komentar (opsional)
                </label>

                <textarea
                    id="comment"
                    name="comment"
                    rows="4"
                    placeholder="Tulis komentar Anda..."
                    class="mt-1 w-full rounded-xl border border-white/25 bg-white/15 px-3 py-2 text-white placeholder:text-white/40 backdrop-blur focus:outline-none focus:ring-2 focus:ring-white/50"
                >{{ old('comment') }}</textarea>

                <x-input-error class="mt-2 text-red-200" :messages="$errors->get('comment')" />
            </section>

            <button
                type="submit"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/25 px-5 py-3 text-base font-semibold text-white shadow-lg transition duration-200 hover:-translate-y-0.5 hover:bg-white/35 focus:outline-none focus:ring-2 focus:ring-white/50"
            >
                Kirim Survey
            </button>

            <div class="pt-1 text-center">
                <a href="{{ url('/') }}" class="text-sm font-medium text-white/70 underline hover:text-white">
                    Lewati survey
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
