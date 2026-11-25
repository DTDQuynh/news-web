<section class="login-panel" aria-labelledby="register-heading">
    <h1 id="register-heading" class="text-2xl font-bold text-slate-900">
        Đăng ký
    </h1>
    <p class="mt-2 text-sm text-slate-600">
        Tạo tài khoản để sử dụng các tính năng của chúng tôi.
    </p>

    @if ($errors->any())
        <x-alert type="error" class="mt-4">
            Vui lòng kiểm tra lại thông tin đăng ký.
        </x-alert>
    @endif

    <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="_auth_panel" value="register">

        <div>
            <label for="register-name" class="mb-1 block text-sm font-medium text-slate-700">
                Họ và tên
            </label>
            <input
                id="register-name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                autocomplete="name"
                required
                class="w-full rounded border border-slate-300 px-3 py-2 text-slate-900"
            >
            @error('name')
                <p class="mt-1 text-sm text-rose-700" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="register-email" class="mb-1 block text-sm font-medium text-slate-700">
                Email
            </label>
            <input
                id="register-email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email"
                required
                class="w-full rounded border border-slate-300 px-3 py-2 text-slate-900"
            >
            @error('email')
                <p class="mt-1 text-sm text-rose-700" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="register-password" class="mb-1 block text-sm font-medium text-slate-700">
                Mật khẩu
            </label>
            <input
                id="register-password"
                type="password"
                name="password"
                autocomplete="new-password"
                required
                class="w-full rounded border border-slate-300 px-3 py-2 text-slate-900"
            >
            @error('password')
                <p class="mt-1 text-sm text-rose-700" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="register-password-confirmation" class="mb-1 block text-sm font-medium text-slate-700">
                Xác nhận mật khẩu
            </label>
            <input
                id="register-password-confirmation"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                required
                class="w-full rounded border border-slate-300 px-3 py-2 text-slate-900"
            >
        </div>

        <button type="submit" class="login-panel__submit">
            Đăng ký
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        Đã có tài khoản?
        <a
            href="{{ route('login') }}"
            data-auth-panel-show="login"
            class="font-medium text-blue-700 hover:underline"
        >
            Đăng nhập
        </a>
    </p>
</section>
