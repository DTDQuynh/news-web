@php($showRegister = $errors->any() && old('_auth_panel') === 'register')

<dialog
    class="login-modal"
    data-login-modal
    @if ($errors->any()) data-open-on-load @endif
    aria-labelledby="{{ $showRegister ? 'register-heading' : 'login-heading' }}"
>
    <div class="login-modal__content">
        <form method="dialog">
            <button type="submit" class="login-modal__close" aria-label="Đóng">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="m6 6 12 12M18 6 6 18"></path>
                </svg>
            </button>
        </form>

        <div data-auth-panel="login" @if ($showRegister) hidden @endif>
            @include('partials.login-panel')
        </div>
        <div data-auth-panel="register" @unless ($showRegister) hidden @endunless>
            @include('partials.register-panel')
        </div>
    </div>
</dialog>
