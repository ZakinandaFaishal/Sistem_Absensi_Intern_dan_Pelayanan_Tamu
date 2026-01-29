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
                <x-text-input
                    id="email"
                    name="email"
                    type="email"
                    class="mt-2 block w-full !rounded-xl
                           !border-emerald-300/10 !bg-white/5 !text-white
                           placeholder:!text-white/40
                           focus:!border-emerald-300/40 focus:!ring-emerald-200/20"
                    :value="old('email', $user->email)"
                    required />
            </div>

            {{-- TOKEN --}}
            <div class="rounded-2xl border border-emerald-300/10 bg-white/5 p-4">
                <x-input-label for="epikir_letter_token"
                    value="Nomor Surat e-Pikir"
                    class="text-[11px] uppercase tracking-wider text-white/70" />

                <x-text-input
                    id="epikir_letter_token"
                    name="epikir_letter_token"
                    type="text"
                    class="mt-2 block w-full !rounded-xl
                           !border-emerald-300/10 !bg-white/5 !text-white
                           placeholder:!text-white/40
                           focus:!border-emerald-300/40 focus:!ring-emerald-200/20"
                    :value="old('epikir_letter_token', $user->epikir_letter_token)"
                    placeholder="070/028/16/2026" />

                <div class="mt-2 rounded-xl border border-emerald-300/15 bg-emerald-400/8 px-3 py-2">
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
