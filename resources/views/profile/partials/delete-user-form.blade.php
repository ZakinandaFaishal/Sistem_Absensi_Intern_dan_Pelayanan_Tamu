{{-- resources/views/profile/partials/delete-user-form.blade.php --}}

<section class="relative overflow-hidden rounded-[22px] border border-emerald-300/15 bg-white/8 p-5 sm:p-6">
    {{-- top accent (GREEN ONLY) --}}
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400/70 via-teal-400/45 to-transparent"></div>

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <div class="h-11 w-11 rounded-2xl bg-rose-500/15 border border-rose-300/20 flex items-center justify-center">
            <x-icon name="trash" class="h-5 w-5 text-rose-100" />
        </div>

        <div class="min-w-0">
            <p class="text-sm font-extrabold text-white truncate">{{ __('Delete Account') }}</p>
            <p class="text-xs text-white/75">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
            </p>
        </div>
    </div>

    {{-- BODY --}}
    <div class="mt-5 space-y-4">
        <div class="rounded-2xl border border-rose-300/20 bg-rose-500/10 p-4">
            <p class="text-sm text-white/85 leading-relaxed">
                <span class="font-semibold text-rose-200">Perhatian:</span>
                {{ __('Before deleting your account, please download any data or information that you wish to retain.') }}
            </p>
        </div>

        <button
            x-data
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center justify-center gap-2 rounded-xl
                   bg-rose-500/15 px-5 py-3 text-sm font-semibold text-rose-100
                   border border-rose-300/20
                   hover:bg-rose-500/22 transition
                   active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-rose-200/25"
        >
            <x-icon name="trash" class="h-5 w-5" />
            {{ __('Delete Account') }}
        </button>
    </div>

    {{-- MODAL --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="relative">
            {{-- top accent (green base + danger hint) --}}
            <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400/55 via-rose-400/35 to-transparent"></div>

            <form method="post" action="{{ route('profile.destroy') }}" class="p-5 sm:p-6 space-y-5">
                @csrf
                @method('delete')

                {{-- Header --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-emerald-300/15 bg-white/8">
                            <x-icon name="trash" class="h-5 w-5 text-rose-200" />
                        </span>

                        <div class="min-w-0">
                            <p class="text-base sm:text-lg font-extrabold tracking-tight text-white">
                                {{ __('Hapus akun?') }}
                            </p>
                            <p class="mt-1 text-sm leading-relaxed text-white/75">
                                {{ __('Penghapusan bersifat permanen. Semua data dan resource akun akan terhapus. Masukkan password untuk konfirmasi.') }}
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="shrink-0 inline-flex h-10 w-10 items-center justify-center rounded-2xl
                               border border-emerald-300/15 bg-white/8 text-white/80
                               hover:bg-white/12 hover:text-white transition
                               active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-white/20"
                        aria-label="Close"
                    >
                        ✕
                    </button>
                </div>

                {{-- Divider (green-ish) --}}
                <div class="h-px w-full bg-gradient-to-r from-transparent via-emerald-300/25 to-transparent"></div>

                {{-- Password --}}
                <div class="rounded-2xl border border-emerald-300/10 bg-white/5 p-4">
                    <x-input-label
                        for="password"
                        value="{{ __('Password') }}"
                        class="text-[11px] uppercase tracking-wider text-white/70"
                    />

                    <div class="mt-2 relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                            <x-icon name="lock-closed" class="h-5 w-5" />
                        </span>

                        <x-text-input
                            id="password"
                            name="password"
                            type="password"
                            class="block w-full pl-10
                                   !rounded-xl
                                   !border-emerald-300/10
                                   !bg-white/5
                                   !text-white
                                   placeholder:!text-white/40
                                   focus:!border-rose-300/35
                                   focus:!ring-rose-200/20"
                            placeholder="{{ __('Masukkan password') }}"
                        />
                    </div>

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-rose-200" />
                </div>

                {{-- Actions --}}
                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2">
                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="inline-flex items-center justify-center rounded-xl
                               bg-white/10 px-5 py-2.5 text-sm font-semibold text-white
                               border border-emerald-300/15 shadow-sm shadow-black/10
                               hover:bg-white/15 transition
                               active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-white/20"
                    >
                        {{ __('Cancel') }}
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl
                               bg-rose-500/70 px-5 py-2.5 text-sm font-semibold text-white
                               border border-rose-300/25 shadow-lg shadow-rose-900/20
                               hover:bg-rose-500/80 transition
                               active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-rose-200/25"
                    >
                        {{ __('Delete Account') }}
                    </button>
                </div>

                {{-- Hint --}}
                <div class="rounded-2xl border border-emerald-300/10 bg-white/5 px-4 py-3">
                    <p class="text-xs text-white/75 leading-relaxed">
                        <span class="font-semibold text-white">Tips:</span>
                        Kalau masih ragu, pilih <span class="font-semibold text-white">Cancel</span>.
                        Penghapusan akun bersifat <span class="font-semibold text-rose-200">permanen</span>.
                    </p>
                </div>
            </form>
        </div>
    </x-modal>
</section>
