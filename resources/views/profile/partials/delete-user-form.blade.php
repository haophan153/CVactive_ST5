<section class="space-y-5">
    <header>
        <h2 class="text-lg font-bold text-rose-700">Xoa tai khoan</h2>
        <p class="mt-1 text-sm text-slate-500">Khi tai khoan bi xoa, tat ca du lieu cua ban se bi xoa vinh vien. Hay chac chan rang ban da tai cac du lieu can thiet truoc khi tiep tuc.</p>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Ban co Chac chan muon xoa tai khoan? Hanh dong nay khong the hoan tac.')">
        @csrf
        @method('delete')

        <div>
            <x-input-label for="password" value="Nhap mat khau de xac nhan" class="text-slate-700" />
            <x-text-input id="password" name="password" type="password"
                class="mt-1.5 block w-full rounded-xl border-slate-200 focus:border-rose-400 focus:ring-rose-300"
                autocomplete="current-password" />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <div class="pt-2">
            <x-danger-button class="bg-rose-600 hover:bg-rose-700 active:scale-[0.98] transition">
                Xoa tai khoan
            </x-danger-button>
        </div>
    </form>
</section>
