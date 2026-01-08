<x-guest-layout>
    <div class="relative max-w-lg mx-auto">

        <form method="POST"
              action="{{ route('guest.survey.store', $visit) }}"
              class="relative space-y-6 rounded-3xl
                     border border-white/20 bg-white/10
                     backdrop-blur-xl shadow-xl p-6">
            @csrf

            {{-- Header --}}
            <div class="text-center space-y-2">
                <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-2xl
                            border border-white/20 bg-white/15 backdrop-blur shadow-lg">
                    ⭐
                </div>

                <h1 class="text-2xl font-extrabold tracking-tight text-white drop-shadow">
                    Survey Kepuasan
                </h1>
                <p class="text-sm text-white/70">
                    Opsional. Mohon berikan penilaian Anda.
                </p>

                <div class="mx-auto mt-4 h-[2px] w-24 rounded-full bg-gradient-to-r
                            from-transparent via-white/40 to-transparent"></div>
            </div>

            {{-- Rating --}}
            <div class="space-y-1">
                <label class="text-sm font-semibold text-white/90">
                    Rating (1–5)
                </label>

                <select name="rating"
                        required
                        class="mt-1 w-full rounded-xl border border-white/25
                               bg-white/15 backdrop-blur
                               px-3 py-2 text-white
                               focus:outline-none focus:ring-2 focus:ring-white/50">
                    <option value="" class="text-slate-900">Pilih</option>
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" class="text-slate-900">
                            {{ $i }}
                        </option>
                    @endfor
                </select>

                <x-input-error class="mt-2 text-white" :messages="$errors->get('rating')" />
            </div>

            {{-- Comment --}}
            <div class="space-y-1">
                <label for="comment" class="text-sm font-semibold text-white/90">
                    Komentar (opsional)
                </label>

                <textarea id="comment"
                          name="comment"
                          rows="4"
                          class="mt-1 w-full rounded-xl border border-white/25
                                 bg-white/15 backdrop-blur
                                 px-3 py-2 text-white
                                 placeholder:text-white/40
                                 focus:outline-none focus:ring-2 focus:ring-white/50"
                          placeholder="Tulis komentar Anda..."></textarea>

                <x-input-error class="mt-2 text-white" :messages="$errors->get('comment')" />
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2
                           rounded-xl bg-white/25 px-5 py-3
                           text-base font-semibold text-white
                           border border-white/30 shadow-lg
                           hover:bg-white/35 hover:-translate-y-0.5
                           transition duration-200
                           focus:outline-none focus:ring-2 focus:ring-white/50">
                Kirim Survey
            </button>

            {{-- Skip --}}
            <div class="pt-1 text-center">
                <a href="{{ url('/') }}"
                   class="text-sm font-medium text-white/70 hover:text-white underline">
                    Lewati survey
                </a>
            </div>

        </form>
    </div>
</x-guest-layout>
