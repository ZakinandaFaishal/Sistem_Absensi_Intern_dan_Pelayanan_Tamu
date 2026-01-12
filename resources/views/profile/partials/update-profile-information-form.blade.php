<section class="relative rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-xl overflow-hidden">
    {{-- inner highlight --}}
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-transparent">
    </div>

    {{-- TOAST (popup sukses) --}}
    @if (session('status') === 'profile-updated' || session('status') === 'verification-link-sent')
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.200ms x-init="setTimeout(() => show = false, 2600)"
            class="fixed top-5 right-5 z-[9999]" style="display:none;">
            <div
                class="rounded-2xl border border-white/15 bg-white/15 backdrop-blur-xl shadow-2xl px-4 py-3 w-[320px] max-w-[calc(100vw-2.5rem)]">
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-10 w-10 rounded-2xl bg-emerald-400/15 border border-emerald-300/20 flex items-center justify-center">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-white">
                            @if (session('status') === 'profile-updated')
                                Profil berhasil diperbarui
                            @else
                                Email verifikasi dikirim
                            @endif
                        </p>
                        <p class="mt-0.5 text-xs text-white/70">
                            @if (session('status') === 'profile-updated')
                                Perubahan sudah disimpan dan tetap di halaman profil.
                            @else
                                Silakan cek inbox/spam untuk tautan verifikasi.
                            @endif
                        </p>
                    </div>

                    <button type="button" @click="show=false"
                        class="ml-auto shrink-0 h-8 w-8 rounded-xl bg-white/10 border border-white/15 text-white/70 hover:text-white hover:bg-white/20 transition"
                        aria-label="Tutup"><x-icon name="x-mark" class="h-5 w-5" /></button>
                </div>
            </div>
        </div>
    @endif

    <header class="relative px-6 py-5 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div
                class="h-10 w-10 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center shadow">
                <x-icon name="identification" class="h-5 w-5" />
            </div>
            <div>
                <h2 class="text-base font-extrabold text-white">
                    {{ __('Profile Information') }}
                </h2>
                <p class="mt-1 text-sm text-white/70">
                    {{ __("Update your account's profile information and email address.") }}
                </p>
            </div>
        </div>
    </header>

    <div class="relative px-6 py-6">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('patch')

            <div>
                <x-input-label for="name" :value="__('Name')" class="text-white/80" />
                <x-text-input id="name" name="name" type="text"
                    class="mt-1 block w-full !rounded-xl !border-white/15 !bg-white/10 !text-white placeholder:!text-white/40 focus:!border-white/30 focus:!ring-white/20"
                    :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2 text-rose-200" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-white/80" />
                <x-text-input id="email" name="email" type="email"
                    class="mt-1 block w-full !rounded-xl !border-white/15 !bg-white/10 !text-white placeholder:!text-white/40 focus:!border-white/30 focus:!ring-white/20"
                    :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2 text-rose-200" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div class="mt-3 rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
                        <p class="text-sm text-white/80">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification"
                                class="underline text-sm text-white/80 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-white/30">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-emerald-200">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <x-primary-button
                    class="!rounded-xl !bg-white/25 !border !border-white/25 !text-white hover:!bg-white/35 focus:!ring-white/40">
                    {{ __('Save') }}
                </x-primary-button>

                {{-- indikator kecil (opsional) --}}
                @if (session('status') === 'profile-updated')
                    <p class="text-sm text-white/70">
                        {{ __('Saved.') }}
                    </p>
                @endif
            </div>
        </form>
    </div>
</section>
