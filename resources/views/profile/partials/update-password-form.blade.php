<section class="relative rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-xl overflow-hidden">
    {{-- inner highlight --}}
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-transparent"></div>

    <header class="relative px-6 py-5 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center shadow">
                <span class="text-lg">🔒</span>
            </div>
            <div>
                <h2 class="text-base font-extrabold text-white">
                    {{ __('Update Password') }}
                </h2>
                <p class="mt-1 text-sm text-white/70">
                    {{ __('Ensure your account is using a long, random password to stay secure.') }}
                </p>
            </div>
        </div>
    </header>

    <div class="relative px-6 py-6">
        <form method="post" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            @method('put')

            <div>
                <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-white/80" />
                <x-text-input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="mt-1 block w-full !rounded-xl !border-white/15 !bg-white/10 !text-white placeholder:!text-white/40 focus:!border-white/30 focus:!ring-white/20"
                    autocomplete="current-password"
                />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password" :value="__('New Password')" class="text-white/80" />
                <x-text-input
                    id="update_password_password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full !rounded-xl !border-white/15 !bg-white/10 !text-white placeholder:!text-white/40 focus:!border-white/30 focus:!ring-white/20"
                    autocomplete="new-password"
                />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-white/80" />
                <x-text-input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="mt-1 block w-full !rounded-xl !border-white/15 !bg-white/10 !text-white placeholder:!text-white/40 focus:!border-white/30 focus:!ring-white/20"
                    autocomplete="new-password"
                />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <x-primary-button class="!rounded-xl !bg-white/25 !border !border-white/25 !text-white hover:!bg-white/35 focus:!ring-white/40">
                    {{ __('Save') }}
                </x-primary-button>

                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-white/70"
                    >{{ __('Saved.') }}</p>
                @endif
            </div>

            <p class="text-xs text-white/55">
                Tips: gunakan minimal 8 karakter dan kombinasi huruf besar/kecil + angka.
            </p>
        </form>
    </div>
</section>
