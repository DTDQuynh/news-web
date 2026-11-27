<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta name="theme-color" content="#286b4f">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/pwa-192.png') }}">
    @yield('meta')
    @php
        $pwaSavedPostUrls = [];

        if (
            auth()->check()
            && (request()->secure() || in_array(request()->getHost(), ['localhost', '127.0.0.1'], true))
        ) {
            $pwaSavedPostUrls = auth()->user()
                ->savedPosts()
                ->where('posts.is_published', true)
                ->orderByPivot('created_at', 'desc')
                ->limit(20)
                ->get(['posts.slug'])
                ->map(fn ($post) => route('posts.show', ['post' => $post->slug]))
                ->all();
        }
    @endphp
    <script>
        window.newsSavedPostUrls = @json($pwaSavedPostUrls);
        window.newsStaticAssetUrls = @json([
            \Illuminate\Support\Facades\Vite::asset('resources/css/app.css'),
            \Illuminate\Support\Facades\Vite::asset('resources/js/app.js'),
        ]);
    </script>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>@yield('title', config('app.name'))</title>
</head>
<body class="font-sans text-slate-800 antialiased">
    @include('partials.navbar')
    <div
        class="offline-status border-b px-4 py-2 text-center text-sm"
        data-offline-status
        role="status"
        aria-live="polite"
        hidden
    ></div>

    @unless (request()->routeIs('login', 'register'))
        @include('partials.login-modal')
    @endunless

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>
