<section class="relative rounded-3xl border border-rose-300/20 bg-white/10 backdrop-blur-xl shadow-xl overflow-hidden">
    {{-- inner highlight --}}
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-transparent">
    </div>

    <header class="relative px-6 py-5 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div
                class="h-10 w-10 rounded-2xl bg-rose-400/15 border border-rose-300/20 flex items-center justify-center shadow">
                <x-icon name="trash" class="h-5 w-5" />
            </div>
            <div>
                <h2 class="text-base font-extrabold text-white">
                    {{ __('Delete Account') }}
                </h2>
                <p class="mt-1 text-sm text-white/70">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                </p>
            </div>
        </div>
    </header>

    <div class="relative px-6 py-6 space-y-4">
        <p class="text-sm text-white/70">
            {{ __('Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>

        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center justify-center gap-2 rounded-xl
                   bg-rose-500/20 px-5 py-3 text-sm font-semibold text-rose-100
                   border border-rose-300/20 shadow
                   hover:bg-rose-500/30 hover:-translate-y-0.5 transition duration-200
                   focus:outline-none focus:ring-2 focus:ring-rose-200/30">
            <x-icon name="trash" class="h-5 w-5" /> {{ __('Delete Account') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="bg-slate-950/60 border border-white/15 backdrop-blur-xl rounded-2xl p-6">
            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-6">
                @csrf
                @method('delete')

                <div>
                    <h2 class="text-lg font-extrabold text-white">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h2>

                    <p class="mt-2 text-sm text-white/70">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                </div>

                <div>
                    <x-input-label for="password" value="{{ __('Password') }}" class="text-white/80" />

                    <x-text-input id="password" name="password" type="password"
                        class="mt-1 block w-full !rounded-xl !border-white/15 !bg-white/10 !text-white placeholder:!text-white/40 focus:!border-white/30 focus:!ring-white/20"
                        placeholder="{{ __('Password') }}" />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')"
                        class="inline-flex items-center justify-center rounded-xl
                               bg-white/10 px-5 py-3 text-sm font-semibold text-white
                               border border-white/20 shadow
                               hover:bg-white/20 transition
                               focus:outline-none focus:ring-2 focus:ring-white/20">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl
                               bg-rose-600/70 px-5 py-3 text-sm font-semibold text-white
                               border border-rose-200/20 shadow
                               hover:bg-rose-600/80 transition
                               focus:outline-none focus:ring-2 focus:ring-rose-200/30">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</section>
