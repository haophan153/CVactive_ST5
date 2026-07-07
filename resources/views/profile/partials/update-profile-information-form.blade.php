<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-slate-900">Thong tin ca nhan</h2>
        <p class="mt-1 text-sm text-slate-500">Cap nhat thong tin tai khoan va dia chi email cua ban.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Ho va ten" class="text-slate-700" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1.5 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-300"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" class="text-slate-700" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1.5 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-300"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-sm text-amber-800">
                        Dia chi email cua ban chua duoc xac minh.

                        <button form="send-verification" class="underline hover:text-amber-900 font-medium">
                            Gui lai email xac minh.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-emerald-700 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Email xac minh da duoc gui.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] transition shadow-sm">Luu</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-emerald-600 font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Da luu.
                </p>
            @endif
        </div>
    </form>
</section>
