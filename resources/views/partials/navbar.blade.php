<header class="border-b border-gray-200 bg-white text-gray-900">
    <nav class="mx-auto flex max-w-7xl flex-wrap items-center gap-4 px-4 py-4" aria-label="Điều hướng chính">
        <a href="{{ route('home') }}" class="navbar-brand text-3xl font-bold" aria-label="Trang chủ">
            {{ config('app.name') }}
        </a>

        <div class="hidden min-w-52 border-l border-gray-300 pl-4 text-sm sm:block">
            <time datetime="{{ now()->toDateString() }}" class="block text-lg text-slate-800">
                {{ ucfirst(now()->locale('vi')->isoFormat('dddd, DD/MM/YYYY')) }}
            </time>
            <span class="text-slate-500">Bản tin hôm nay</span>
        </div>

        <div class="navbar-actions">
            <form action="{{ route('search') }}" method="GET" role="search" class="navbar-search__form">
                <label for="navbar-search-query" class="sr-only">Tìm kiếm tin tức</label>
                <input
                    id="navbar-search-query"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Tìm kiếm tin tức..."
                    class="navbar-search__input"
                >
                <button type="submit" class="navbar-search__submit" aria-label="Tìm kiếm">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <circle cx="10.8" cy="10.8" r="7.2"></circle>
                        <path d="m16.2 16.2 4.2 4.2"></path>
                    </svg>
                </button>
            </form>

            <button
                type="button"
                class="navbar-theme-toggle"
                data-theme-toggle
                aria-label="Bật chế độ tối"
                aria-pressed="false"
            >
                <img
                    src="{{ asset('images/lightbulb-on.png') }}"
                    alt=""
                    aria-hidden="true"
                    class="navbar-theme-toggle__icon navbar-theme-toggle__icon--on"
                >
                <img
                    src="{{ asset('images/lightbulb-off.png') }}"
                    alt=""
                    aria-hidden="true"
                    class="navbar-theme-toggle__icon navbar-theme-toggle__icon--off"
                >
            </button>

            <div class="navbar-account-group">
                @auth
                    <span class="navbar-account">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <circle cx="12" cy="8" r="3.5"></circle>
                            <path d="M5 20c0-3.4 3.1-6 7-6s7 2.6 7 6"></path>
                        </svg>
                        <span>Xin chào {{ auth()->user()->name }}</span>
                    </span>

                    <a
                        href="{{ route('profile.saved') }}"
                        class="navbar-profile-icon"
                        data-tooltip="Bài viết đã lưu"
                        aria-label="Bài viết đã lưu"
                    >
                        <img src="{{ asset('images/bookmark-icon.png') }}" alt="" aria-hidden="true">
                        <span class="sr-only">Bài viết đã lưu</span>
                    </a>
                    <a
                        href="{{ route('profile.history') }}"
                        class="navbar-profile-icon"
                        data-tooltip="Lịch sử đã đọc"
                        aria-label="Lịch sử đã đọc"
                    >
                        <img src="{{ asset('images/history-icon.png') }}" alt="" aria-hidden="true">
                        <span class="sr-only">Lịch sử đã đọc</span>
                    </a>

                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="navbar-admin-link">Quản trị</a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="navbar-logout">
                        @csrf
                        <button type="submit" class="navbar-logout__button">Đăng xuất</button>
                    </form>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="navbar-account"
                        data-login-modal-open
                        aria-haspopup="dialog"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <circle cx="12" cy="8" r="3.5"></circle>
                            <path d="M5 20c0-3.4 3.1-6 7-6s7 2.6 7 6"></path>
                        </svg>
                        <span>Đăng nhập</span>
                    </a>
                @endauth
            </div>

        </div>
    </nav>

    <div class="mx-auto flex max-w-7xl flex-wrap gap-4 px-4 pb-4" aria-label="Danh mục">
        @foreach ($categories as $category)
            @php($isActiveCategory = request()->routeIs('categories.show') && request()->route('slug') === $category['slug'])
            <a
                href="{{ route('categories.show', $category['slug']) }}"
                @class(['navbar-category-link', 'is-active' => $isActiveCategory])
                @if ($isActiveCategory) aria-current="page" @endif
            >
                {{ $category['name'] }}
            </a>
        @endforeach
    </div>
</header>
