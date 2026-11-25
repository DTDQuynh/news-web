<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị') - {{ config('app.name', 'News') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row min-vh-100">
            <aside class="col-md-3 col-lg-2 bg-dark text-white p-3 d-flex flex-column">
                <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none fw-bold fs-5 mb-4">
                    <i class="fa-solid fa-newspaper text-primary me-2"></i>Quản trị
                </a>

                <ul class="nav nav-pills flex-column gap-1 mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                           class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-gauge me-2"></i>Tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.categories.index') }}"
                           class="nav-link text-white {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-folder me-2"></i>Danh mục
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#"
                           class="nav-link text-white {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-lines me-2"></i>Bài viết
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#"
                           class="nav-link text-white {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-comments me-2"></i>Bình luận
                        </a>
                    </li>
                </ul>

                <hr class="border-secondary">

                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light">
                    <i class="fa-solid fa-arrow-left me-1"></i> Về trang chủ
                </a>
            </aside>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                    <h1 class="h4 mb-0">@yield('heading', 'Tổng quan')</h1>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted small">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">Đăng xuất</button>
                        </form>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>