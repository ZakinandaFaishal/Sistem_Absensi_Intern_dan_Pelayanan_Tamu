<section class="relative overflow-hidden rounded-[22px] border border-white/12 bg-white/8 p-5 sm:p-6">
    {{-- top accent --}}
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-sky-400/60 via-fuchsia-400/55 to-emerald-400/55"></div>

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <div
            class="h-11 w-11 rounded-2xl bg-rose-500/15 border border-rose-300/15
                   flex items-center justify-center"
        >
            <x-icon name="trash" class="h-5 w-5 text-rose-100" />
        </div>

        <div class="min-w-0">
            <p class="text-sm font-extrabold text-white truncate">
                {{ __('Delete Account') }}
            </p>
            <p class="text-xs text-white/75">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
            </p>
        </div>
    </div>

    {{-- BODY --}}
    <div class="mt-5 space-y-4">
        <div class="rounded-2xl border border-amber-300/15 bg-amber-400/8 p-4">
            <p class="text-sm text-white/85 leading-relaxed">
                <span class="font-semibold text-rose-400 ">Perhatian:</span>
                {{ __('Before deleting your account, please download any data or information that you wish to retain.') }}
            </p>
        </div>

        <button
            x-data
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center justify-center gap-2 rounded-xl
                   bg-rose-500/15 px-5 py-3 text-sm font-semibold text-rose-100
                   border border-rose-300/15
                   hover:bg-rose-500/22 transition
                   focus:outline-none focus:ring-2 focus:ring-rose-200/25"
        >
            <x-icon name="trash" class="h-5 w-5" />
            {{ __('Delete Account') }}
        </button>
    </div>

    {{-- MODAL --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="relative overflow-hidden rounded-[22px] border border-white/12 bg-white/8 p-5 sm:p-6">
            {{-- modal accent --}}
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-400/60 via-indigo-400/55 to-white/25"></div>

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-5">
                @csrf
                @method('delete')

                <div class="space-y-1">
                    <p class="text-sm font-extrabold text-white">
                        {{ __('Are you sure you want to delete your account?') }}
                    </p>
                    <p class="text-xs text-white/75 leading-relaxed">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <x-input-label
                        for="password"
                        value="{{ __('Password') }}"
                        class="text-[11px] uppercase tracking-wider text-white/70"
                    />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-2 block w-full
                               !rounded-xl
                               !border-white/10
                               !bg-white/5
                               !text-white
                               placeholder:!text-white/40
                               focus:!border-amber-300/25
                               focus:!ring-amber-200/20"
                        placeholder="{{ __('Password') }}"
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-amber-100" />
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-1">
                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="inline-flex items-center justify-center rounded-xl
                               bg-white/10 px-5 py-3 text-sm font-semibold text-white
                               border border-white/12
                               hover:bg-white/15 transition
                               focus:outline-none focus:ring-2 focus:ring-white/20"
                    >
                        {{ __('Cancel') }}
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl
                               bg-amber-500/55 px-5 py-3 text-sm font-semibold text-white
                               border border-amber-300/25
                               hover:bg-amber-500/70 transition
                               focus:outline-none focus:ring-2 focus:ring-amber-200/25"
                    >
                        {{ __('Delete Account') }}
                    </button>
                </div>

                {{-- helper hint --}}
                <div class="rounded-xl border border-indigo-300/15 bg-indigo-400/8 px-3 py-2">
                    <p class="text-[12px] text-white/80">
                        Tips: Jika ragu, pilih <span class="font-semibold text-indigo-100">Cancel</span>.
                        Penghapusan akun bersifat <span class="font-semibold text-amber-100">permanen</span>.
                    </p>
                </div>
            </form>
        </div>
    </x-modal>
</section>
