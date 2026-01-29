<section class="relative overflow-hidden rounded-[22px] border border-white/10 bg-[#111827]/55 backdrop-blur-xl shadow-[0_18px_55px_rgba(0,0,0,.45)] p-5 sm:p-6">

    {{-- top accent (single tone) --}}
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-teal-300/45 via-white/10 to-transparent"></div>

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <div class="h-11 w-11 rounded-2xl bg-white/6 border border-white/12 flex items-center justify-center">
            <x-icon name="users" class="h-5 w-5 text-white/60" />
        </div>
        <div class="min-w-0">
            <p class="text-sm font-extrabold text-white truncate">Update Profil & Password</p>
            <p class="text-xs text-white/60">Satu tombol untuk profil, e-Pikir, dan password.</p>
        </div>
    </div>

    {{-- TOAST --}}
    @if (session('status') === 'profile-password-updated')
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.200ms x-init="setTimeout(() => show = false, 2600)"
            class="mt-4" style="display:none;">
            <div class="rounded-2xl border border-white/12 bg-white/6 px-4 py-3 text-sm text-white flex items-start gap-2">
                <x-icon name="check-circle" class="h-5 w-5 mt-0.5 text-teal-200/90" />
                <div class="leading-snug text-white/90">Profil & password berhasil diperbarui.</div>

                <button type="button" @click="show=false"
                    class="ml-auto h-8 w-8 rounded-xl border border-white/12 bg-white/8 text-white/70 hover:bg-white/12 hover:text-white transition flex items-center justify-center"
                    aria-label="Tutup">
                    <x-icon name="x-mark" class="h-4 w-4" />
                </button>
            </div>
        </div>
    @endif

    @php
        $profileUser = $user ?? auth()->user();
        $isIntern = ($profileUser?->role ?? null) === 'intern';
        $mustChangePassword = (bool) ($profileUser?->must_change_password ?? false);
    @endphp

    <div class="mt-5">
        <form method="post" action="{{ route('profile.updateCombined') }}" class="space-y-4">
            @csrf
            @method('put')

            {{-- NAME --}}
            <div class="rounded-2xl border border-white/10 bg-white/6 p-4">
                <x-input-label for="name" :value="__('Name')"
                    class="text-[11px] uppercase tracking-wider text-white/55" />
                <x-text-input id="name" name="name" type="text"
                    class="mt-2 block w-full !rounded-xl !border-white/10 !bg-white/5 !text-white
                           placeholder:!text-white/40
                           focus:!border-teal-300/25 focus:!ring-teal-200/20"
                    :value="old('name', $profileUser->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2 text-rose-100" :messages="$errors->get('name')" />
            </div>

            {{-- EMAIL --}}
            <div class="rounded-2xl border border-white/10 bg-white/6 p-4">
                <x-input-label for="email" :value="__('Email')"
                    class="text-[11px] uppercase tracking-wider text-white/55" />
                <input type="hidden" name="email" value="{{ $profileUser->email }}">
                <x-text-input id="email" name="email_display" type="email"
                    class="mt-2 block w-full !rounded-xl !border-white/10 !bg-white/5 !text-white/70
                           placeholder:!text-white/40"
                    :value="$profileUser->email" required autocomplete="username" disabled />
                <x-input-error class="mt-2 text-rose-100" :messages="$errors->get('email')" />
            </div>

            {{-- TOKEN --}}
            <div class="rounded-2xl border border-white/10 bg-white/6 p-4">
                <x-input-label for="epikir_letter_token"
                    :value="$isIntern ? 'Nomor Surat e-Pikir (wajib)' : 'Nomor Surat e-Pikir (opsional)'"
                    class="text-[11px] uppercase tracking-wider text-white/55" />

                <x-text-input id="epikir_letter_token" name="epikir_letter_token" type="text"
                    class="mt-2 block w-full !rounded-xl !border-white/10 !bg-white/5 !text-white
                           placeholder:!text-white/40
                           focus:!border-teal-300/25 focus:!ring-teal-200/20"
                    :value="old('epikir_letter_token', $profileUser->epikir_letter_token)"
                    placeholder="Contoh: 070/028/16/2026" />

                <p class="mt-2 text-[12px] text-white/60">Nomor surat ini disimpan dan digunakan untuk validasi.</p>

                <div class="mt-2 rounded-xl border border-white/12 bg-white/6 px-3 py-2">
                    <p class="text-[12px] text-white/75">
                        @if ($isIntern)
                            Wajib diisi sesuai <span class="font-semibold text-white">Nomor Surat</span> dari e-Pikir.
                            Format: <span class="font-semibold text-white">070/028/16/2026</span>.
                        @else
                            Diisi jika diperlukan untuk validasi pendaftaran dari e-Pikir.
                        @endif
                    </p>
                </div>

                <x-input-error class="mt-2 text-rose-100" :messages="$errors->get('epikir_letter_token')" />
            </div>

            {{-- PASSWORD --}}
            <div class="rounded-2xl border border-white/10 bg-white/6 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-[11px] uppercase tracking-wider text-white/55">Password</p>

                    @if ($mustChangePassword)
                        <span class="inline-flex items-center rounded-full bg-white/10 border border-white/12 px-3 py-1 text-[11px] font-semibold text-white">
                            Wajib ganti password
                        </span>
                    @else
                        <span class="text-[11px] text-white/55">Opsional (kosongkan jika tidak diubah)</span>
                    @endif
                </div>

                <div class="mt-3 grid grid-cols-1 gap-4">
                    <div>
                        <x-input-label for="update_profile_current_password" :value="__('Current Password')"
                            class="text-[11px] uppercase tracking-wider text-white/55" />
                        <x-text-input id="update_profile_current_password" name="current_password" type="password"
                            class="mt-2 block w-full !rounded-xl !border-white/10 !bg-white/5 !text-white
                                   placeholder:!text-white/40
                                   focus:!border-teal-300/25 focus:!ring-teal-200/20"
                            autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2 text-rose-100" />
                    </div>

                    <div>
                        <x-input-label for="update_profile_password" :value="__('New Password')"
                            class="text-[11px] uppercase tracking-wider text-white/55" />
                        <x-text-input id="update_profile_password" name="password" type="password"
                            class="mt-2 block w-full !rounded-xl !border-white/10 !bg-white/5 !text-white
                                   placeholder:!text-white/40
                                   focus:!border-teal-300/25 focus:!ring-teal-200/20"
                            autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-100" />
                    </div>

                    <div>
                        <x-input-label for="update_profile_password_confirmation" :value="__('Confirm Password')"
                            class="text-[11px] uppercase tracking-wider text-white/55" />
                        <x-text-input id="update_profile_password_confirmation" name="password_confirmation" type="password"
                            class="mt-2 block w-full !rounded-xl !border-white/10 !bg-white/5 !text-white
                                   placeholder:!text-white/40
                                   focus:!border-teal-300/25 focus:!ring-teal-200/20"
                            autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-rose-100" />
                    </div>
                </div>

                <div class="mt-3 rounded-xl border border-white/12 bg-white/6 px-3 py-2">
                    <p class="text-[12px] text-white/75">
                        Tips: gunakan minimal <span class="font-semibold text-white">8 karakter</span> dan kombinasi
                        <span class="font-semibold text-white">huruf besar/kecil</span> + <span class="font-semibold text-white">angka</span>.
                    </p>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-1">
                <x-primary-button
                    class="!rounded-xl
                           !bg-teal-400/15
                           !border !border-teal-300/20
                           !text-white
                           hover:!bg-teal-400/22
                           focus:!ring-teal-200/20">
                    {{ __('Save') }}
                </x-primary-button>
            </div>

        </form>
    </div>
</section>
