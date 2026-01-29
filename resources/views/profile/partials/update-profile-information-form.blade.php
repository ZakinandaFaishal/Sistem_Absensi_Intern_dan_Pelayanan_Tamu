<section class="relative overflow-hidden rounded-[22px] border border-emerald-300/15 bg-white/8 p-5 sm:p-6">
    {{-- top accent --}}
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400/70 via-teal-400/45 to-transparent"></div>

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <div class="h-11 w-11 rounded-2xl bg-emerald-400/10 border border-emerald-300/20 flex items-center justify-center">
            <x-icon name="users" class="h-5 w-5 text-emerald-100" />
        </div>
        <div class="min-w-0">
            <p class="text-sm font-extrabold text-white truncate">{{ __('Update Profile') }}</p>
            <p class="text-xs text-white/75">{{ __("Update your account's profile information and email address.") }}</p>
        </div>
    </div>

    {{-- TOAST --}}
    @if (session('status') === 'profile-updated' || session('status') === 'verification-link-sent')
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.200ms
             x-init="setTimeout(() => show = false, 2600)"
             class="mt-4" style="display:none;">
            <div class="rounded-2xl border border-emerald-300/25 bg-emerald-400/12 px-4 py-3 text-sm text-white flex items-start gap-2">
                <x-icon name="check-circle" class="h-5 w-5 mt-0.5 text-emerald-100" />
                <div class="leading-snug">
                    {{ session('status') === 'profile-updated'
                        ? 'Profil berhasil diperbarui.'
                        : 'Link verifikasi baru sudah dikirim ke email Anda.' }}
                </div>
            </div>
        </div>
    @endif

    <div class="mt-5">
        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            {{-- NAME --}}
            <div class="rounded-2xl border border-emerald-300/10 bg-white/5 p-4">
                <x-input-label for="name" value="Name"
                    class="text-[11px] uppercase tracking-wider text-white/70" />
                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="mt-2 block w-full !rounded-xl
                           !border-emerald-300/10 !bg-white/5 !text-white
                           placeholder:!text-white/40
                           focus:!border-emerald-300/40 focus:!ring-emerald-200/20"
                    :value="old('name', $user->name)"
                    required />
            </div>

            {{-- EMAIL --}}
            <div class="rounded-2xl border border-emerald-300/10 bg-white/5 p-4">
                <x-input-label for="email" value="Email"
                    class="text-[11px] uppercase tracking-wider text-white/70" />
                <input type="hidden" name="email" value="{{ $user->email }}">
                <x-text-input id="email" name="email_display" type="email"
                    class="mt-2 block w-full !rounded-xl !border-white/10 !bg-white/5 !text-white/80
                           placeholder:!text-white/40"
                    :value="$user->email" required autocomplete="username" disabled />
                <x-input-error class="mt-2 text-rose-100" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div class="mt-3 rounded-2xl border border-sky-300/15 bg-sky-400/8 p-4">
                        <p class="text-xs text-white/80 leading-relaxed">
                            <span
                                class="font-semibold text-sky-100">{{ __('Your email address is unverified.') }}</span>
                            <button form="send-verification"
                                class="ml-1 underline underline-offset-2 text-sky-100 hover:text-white
                                       focus:outline-none focus:ring-2 focus:ring-white/20 rounded">
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
            <div class="rounded-2xl border border-emerald-300/10 bg-white/5 p-4">
                <x-input-label for="epikir_letter_token"
                    value="Nomor Surat e-Pikir"
                    class="text-[11px] uppercase tracking-wider text-white/70" />

                <p class="mt-2 text-[12px] text-white/70">Nomor surat ini disimpan dan digunakan untuk validasi.</p>

                <div class="mt-2 rounded-xl border border-fuchsia-300/15 bg-fuchsia-400/8 px-3 py-2">
                    <p class="text-[12px] text-white/80">
                        Digunakan untuk validasi surat dari e-Pikir.
                    </p>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="flex items-center gap-3 pt-2">
                <x-primary-button
                    class="!rounded-xl
                           !bg-emerald-400/20
                           !border !border-emerald-300/30
                           !text-emerald-100
                           hover:!bg-emerald-400/30
                           focus:!ring-emerald-200/25">
                    Save
                </x-primary-button>
            </div>
        </form>
    </div>
</section>
