<section class="relative overflow-hidden rounded-[22px] border border-emerald-300/15 bg-white/8 p-5 sm:p-6">
    {{-- top accent (GREEN ONLY) --}}
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400/70 via-teal-400/45 to-transparent"></div>

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        {{-- boleh tetap amber biar "security" beda, tapi border-nya tetap hijau --}}
        <div class="h-11 w-11 rounded-2xl bg-emerald-400/10 border border-emerald-300/20 flex items-center justify-center">
            <x-icon name="lock-closed" class="h-5 w-5 text-emerald-100" />
        </div>

        <div class="min-w-0">
            <p class="text-sm font-extrabold text-white truncate">{{ __('Update Password') }}</p>
            <p class="text-xs text-white/75">
                {{ __('Ensure your account is using a long, random password to stay secure.') }}
            </p>
        </div>
    </div>

    {{-- BODY --}}
    <div class="mt-5">
        @php
            $profileUser = $user ?? auth()->user();
            $isIntern = ($profileUser?->role ?? null) === 'intern';
            $epikir = trim((string) ($profileUser?->epikir_letter_token ?? ''));
            $epikirValid = (bool) preg_match('/^\d{1,4}\/\d{1,4}\/\d{1,4}\/\d{4}$/', $epikir);
            $canChangePassword = !$isIntern || ($epikir !== '' && $epikirValid);
        @endphp

        @if (!$canChangePassword)
            <div
                class="mb-4 rounded-2xl border border-fuchsia-300/15 bg-fuchsia-400/10 px-4 py-3 text-sm text-white/90">
                <div class="flex items-start gap-2">
                    <x-icon name="information-circle" class="h-5 w-5 mt-0.5 text-fuchsia-100" />
                    <div class="leading-snug">
                        <div class="font-semibold text-fuchsia-100">Lengkapi Nomor Surat e-Pikir dulu</div>
                        <div class="text-white/80">Untuk mengganti password, isi dan klik <span
                                class="font-semibold text-white">Save</span> pada form profil (Nomor Surat e-Pikir),
                            lalu coba lagi.</div>
                        <a href="#epikir_letter_token"
                            class="inline-flex mt-2 underline underline-offset-2 text-fuchsia-100 hover:text-white">Ke
                            kolom Nomor Surat e-Pikir</a>
                    </div>
                </div>
            </div>
        @endif

        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <x-input-error :messages="$errors->updatePassword->get('epikir_letter_token')" class="text-rose-100" />

            {{-- CURRENT PASSWORD --}}
            <div class="rounded-2xl border border-emerald-300/10 bg-white/5 p-4">
                <x-input-label
                    for="update_password_current_password"
                    :value="__('Current Password')"
                    class="text-[11px] uppercase tracking-wider text-white/70"
                />
                <x-text-input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="mt-2 block w-full
                           !rounded-xl
                           !border-emerald-300/10
                           !bg-white/5
                           !text-white
                           placeholder:!text-white/40
                           focus:!border-sky-300/25
                           focus:!ring-sky-200/20"
                    autocomplete="current-password" @disabled(!$canChangePassword) />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-rose-100" />
            </div>

            {{-- NEW PASSWORD --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <x-input-label for="update_password_password" :value="__('New Password')"
                    class="text-[11px] uppercase tracking-wider text-white/70" />
                <x-text-input id="update_password_password" name="password" type="password"
                    class="mt-2 block w-full
                           !rounded-xl
                           !border-emerald-300/10
                           !bg-white/5
                           !text-white
                           placeholder:!text-white/40
                           focus:!border-fuchsia-300/25
                           focus:!ring-fuchsia-200/15"
                    autocomplete="new-password" @disabled(!$canChangePassword) />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-rose-100" />
            </div>

            {{-- CONFIRM PASSWORD --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')"
                    class="text-[11px] uppercase tracking-wider text-white/70" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="mt-2 block w-full
                           !rounded-xl
                           !border-emerald-300/10
                           !bg-white/5
                           !text-white
                           placeholder:!text-white/40
                           focus:!border-sky-300/25
                           focus:!ring-sky-200/20"
                    autocomplete="new-password" @disabled(!$canChangePassword) />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-rose-100" />
            </div>

            {{-- ACTION --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-1">
                <x-primary-button
                    class="!rounded-xl
           !bg-emerald-400/20
           !border !border-emerald-300/25
           !text-emerald-100
           hover:!bg-emerald-400/30
           focus:!ring-emerald-200/25"
                    @disabled(!$canChangePassword)>
                    {{ __('Save') }}
                </x-primary-button>



                @if (session('status') === 'password-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                        class="text-xs text-emerald-100">
                        {{ __('Saved.') }}
                    </p>
                @endif
            </div>

            {{-- tips box (GREEN ONLY) --}}
            <div class="rounded-xl border border-emerald-300/15 bg-emerald-400/8 px-3 py-2">
                <p class="text-[12px] text-white/80">
                    Tips: gunakan minimal <span class="font-semibold text-sky-100">8 karakter</span> dan kombinasi
                    <span class="font-semibold text-fuchsia-100">huruf besar/kecil</span> + <span
                        class="font-semibold text-emerald-100">angka</span>.
                </p>
            </div>
        </form>
    </div>
</section>
