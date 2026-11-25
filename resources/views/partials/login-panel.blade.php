<section class="login-panel" aria-labelledby="login-heading">
    <h1 id="login-heading" class="text-2xl font-bold text-slate-900">
        Đăng nhập
    </h1>
    <p class="mt-2 text-sm text-slate-600">
        Đăng nhập để sử dụng tài khoản của bạn.
    </p>

    @if ($errors->any())
        <x-alert type="error" class="mt-4">
            Thông tin đăng nhập chưa chính xác. Vui lòng kiểm tra các trường bên dưới.
        </x-alert>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="_auth_panel" value="login">

        <div>
            <label for="login-email" class="mb-1 block text-sm font-medium text-slate-700">
                Email
            </label>
            <input
                id="login-email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="username"
                required
                class="w-full rounded border border-slate-300 px-3 py-2 text-slate-900"
            >
            @error('email')
                <p class="mt-1 text-sm text-rose-700" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="login-password" class="mb-1 block text-sm font-medium text-slate-700">
                Mật khẩu
            </label>
            <input
                id="login-password"
                type="password"
                name="password"
                autocomplete="current-password"
                required
                class="w-full rounded border border-slate-300 px-3 py-2 text-slate-900"
            >
            @error('password')
                <p class="mt-1 text-sm text-rose-700" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="login-panel__submit"
        >
            Đăng nhập
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        Chưa có tài khoản?
        <a
            href="{{ route('register') }}"
            data-auth-panel-show="register"
            class="font-medium text-blue-700 hover:underline"
        >
            Đăng ký ngay
        </a>
    </p>
</section>
