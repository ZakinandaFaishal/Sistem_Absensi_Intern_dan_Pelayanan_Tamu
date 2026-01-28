<section class="relative overflow-hidden rounded-[22px] border border-white/12 bg-white/8 p-5 sm:p-6">
    {{-- top accent --}}
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-sky-400/60 via-fuchsia-400/55 to-emerald-400/55"></div>

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <div class="h-11 w-11 rounded-2xl bg-sky-400/10 border border-sky-300/20 flex items-center justify-center">
            <x-icon name="users" class="h-5 w-5 text-sky-100" />
        </div>
        <div class="min-w-0">
            <p class="text-sm font-extrabold text-white truncate">{{ __('Update Profile') }}</p>
            <p class="text-xs text-white/75">{{ __("Update your account's profile information and email address.") }}</p>
        </div>
    </div>

    {{-- TOAST INLINE (biar nyatu sama layout) --}}
    @if (session('status') === 'profile-updated' || session('status') === 'verification-link-sent')
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition.opacity.duration.200ms
            x-init="setTimeout(() => show = false, 2600)"
            class="mt-4"
            style="display:none;"
        >
            <div class="rounded-2xl border border-emerald-300/25 bg-emerald-400/12 px-4 py-3 text-sm text-emerald-100 flex items-start gap-2">
                <x-icon name="check-circle" class="h-5 w-5 mt-0.5" />
                <div class="leading-snug text-white/95">
                    @if (session('status') === 'profile-updated')
                        Profil berhasil diperbarui.
                    @else
                        Link verifikasi baru sudah dikirim ke email Anda.
                    @endif
                </div>

                <button type="button" @click="show=false"
                    class="ml-auto h-8 w-8 rounded-xl border border-white/12 bg-white/8 text-white/70 hover:bg-white/12 hover:text-white transition flex items-center justify-center"
                    aria-label="Tutup">
                    <x-icon name="x-mark" class="h-4 w-4" />
                </button>
            </div>
        </div>
    @endif

    <div class="mt-5">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            {{-- NAME --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <x-input-label for="name" :value="__('Name')" class="text-[11px] uppercase tracking-wider text-white/70" />
                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="mt-2 block w-full !rounded-xl !border-white/10 !bg-white/5 !text-white
                           placeholder:!text-white/40 focus:!border-sky-300/25 focus:!ring-sky-200/20"
                    :value="old('name', $user->name)"
                    required autofocus autocomplete="name"
                />
                <x-input-error class="mt-2 text-rose-100" :messages="$errors->get('name')" />
            </div>

            {{-- EMAIL --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <x-input-label for="email" :value="__('Email')" class="text-[11px] uppercase tracking-wider text-white/70" />
                <x-text-input
                    id="email"
                    name="email"
                    type="email"
                    class="mt-2 block w-full !rounded-xl !border-white/10 !bg-white/5 !text-white
                           placeholder:!text-white/40 focus:!border-sky-300/25 focus:!ring-sky-200/20"
                    :value="old('email', $user->email)"
                    required autocomplete="username"
                />
                <x-input-error class="mt-2 text-rose-100" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div class="mt-3 rounded-2xl border border-sky-300/15 bg-sky-400/8 p-4">
                        <p class="text-xs text-white/80 leading-relaxed">
                            <span class="font-semibold text-sky-100">{{ __('Your email address is unverified.') }}</span>
                            <button
                                form="send-verification"
                                class="ml-1 underline underline-offset-2 text-sky-100 hover:text-white
                                       focus:outline-none focus:ring-2 focus:ring-white/20 rounded"
                            >
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-xs font-semibold text-emerald-100">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- TOKEN --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <x-input-label for="epikir_letter_token" value="Token Surat e-Pikir (opsional)" class="text-[11px] uppercase tracking-wider text-white/70" />
                <x-text-input
                    id="epikir_letter_token"
                    name="epikir_letter_token"
                    type="text"
                    class="mt-2 block w-full !rounded-xl !border-white/10 !bg-white/5 !text-white
                           placeholder:!text-white/40 focus:!border-fuchsia-300/25 focus:!ring-fuchsia-200/15"
                    :value="old('epikir_letter_token', $user->epikir_letter_token)"
                    placeholder="Masukkan token/nomor surat dari e-Pikir untuk validasi"
                />

                <div class="mt-2 rounded-xl border border-fuchsia-300/15 bg-fuchsia-400/8 px-3 py-2">
                    <p class="text-[12px] text-white/80">
                        Isi saat diminta untuk <span class="font-semibold text-fuchsia-100">handshake/validasi</span> pendaftaran magang dari e-Pikir.
                    </p>
                </div>

                <x-input-error class="mt-2 text-rose-100" :messages="$errors->get('epikir_letter_token')" />
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
                >
                    {{ __('Save') }}
                </x-primary-button>


                @if (session('status') === 'profile-updated')
                    <p class="text-xs text-emerald-100">
                        {{ __('Saved.') }}
                    </p>
                @endif
            </div>
        </form>
    </div>
</section>
