<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-slate-900">Cap nhat mat khau</h2>
        <p class="mt-1 text-sm text-slate-500">Su dung mat khau manh de bao mat tai khoan cua ban.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <x-input-label for="current_password" value="Mat khau hien tai" class="text-slate-700" />
            <x-text-input id="current_password" name="current_password" type="password"
                class="mt-1.5 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-300"
                autocomplete="current-password" />
            <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
        </div>

        <div>
            <x-input-label for="password" value="Mat khau moi" class="text-slate-700" />
            <x-text-input id="password" name="password" type="password"
                class="mt-1.5 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-300"
                autocomplete="new-password" />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Xac nhan mat khau moi" class="text-slate-700" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                class="mt-1.5 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-300"
                autocomplete="new-password" />
            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] transition shadow-sm">Doi mat khau</x-primary-button>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-emerald-600 font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Da doi mat khau.
                </p>
            @endif
        </div>
    </form>
</section>
