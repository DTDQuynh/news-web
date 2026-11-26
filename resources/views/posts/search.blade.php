@extends('layouts.app')

@php($currentKeyword = request('keyword') ?? request('q'))
@php($pageTitle = filled($currentKeyword) ? 'Tìm kiếm: "' . $currentKeyword . '"' : 'Tìm kiếm & Lọc tin tức')

@section('title', $pageTitle . ' - ' . config('app.name'))

@section('content')
    <section class="news-home" aria-labelledby="search-heading">
        <div class="news-home__heading">
            <h1 id="search-heading" class="news-home__title">
                {{ $pageTitle }}
            </h1>
            <span class="news-home__updated">
                {{ number_format($posts->total(), 0, ',', '.') }} kết quả
            </span>
        </div>

        <form action="{{ route('search') }}"
              method="GET"
              class="mb-6 grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-12 dark:border-slate-700 dark:bg-slate-800">
            <div class="lg:col-span-5">
                <label for="keyword" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Từ khóa
                </label>
                <input type="text"
                       id="keyword"
                       name="keyword"
                       value="{{ $currentKeyword }}"
                       placeholder="Nhập tiêu đề hoặc nội dung tóm tắt..."
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-emerald-600 focus:outline-none dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
            </div>

            <div class="lg:col-span-3">
                <label for="category" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Danh mục
                </label>
                <select id="category"
                        name="category"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-emerald-600 focus:outline-none dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $item)
                        <option value="{{ $item->slug }}"
                            @selected((string) request('category') === (string) $item->slug || (string) request('category') === (string) $item->id)>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2">
                <label for="sort" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Sắp xếp
                </label>
                <select id="sort"
                        name="sort"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-emerald-600 focus:outline-none dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Mới nhất</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Cũ nhất</option>
                    <option value="popular" @selected(in_array(request('sort'), ['popular', 'views_count'], true))>Xem nhiều nhất</option>
                </select>
            </div>

            <div class="flex items-end gap-2 lg:col-span-2">
                <button type="submit"
                        class="w-full rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
                    Lọc tin
                </button>
                @if (filled($currentKeyword) || filled(request('category')) || request('sort', 'latest') !== 'latest')
                    <a href="{{ route('search') }}"
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700"
                       title="Đặt lại bộ lọc">
                        Xóa
                    </a>
                @endif
            </div>
        </form>

        @if ($posts->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        @else
            <p class="news-home__empty">
                Không tìm thấy bài viết nào phù hợp với điều kiện lọc hiện tại.
            </p>
        @endif

        @if ($posts->hasPages())
            <nav class="news-home__pagination" aria-label="Phân trang kết quả tìm kiếm">
                <p class="news-home__pagination-summary">
                    Hiển thị
                    <strong>{{ $posts->firstItem() }}</strong>–<strong>{{ $posts->lastItem() }}</strong>
                    trong <strong>{{ $posts->total() }}</strong> bài viết
                </p>

                <div class="news-home__pagination-controls">
                    @if ($posts->onFirstPage())
                        <span class="news-home__pagination-button" aria-disabled="true">
                            Trước
                        </span>
                    @else
                        <a href="{{ $posts->previousPageUrl() }}"
                           rel="prev"
                           class="news-home__pagination-button">
                            Trước
                        </a>
                    @endif

                    <span class="news-home__pagination-status" aria-current="page">
                        Trang {{ $posts->currentPage() }} / {{ $posts->lastPage() }}
                    </span>

                    @if ($posts->hasMorePages())
                        <a href="{{ $posts->nextPageUrl() }}"
                           rel="next"
                           class="news-home__pagination-button">
                            Sau
                        </a>
                    @else
                        <span class="news-home__pagination-button" aria-disabled="true">
                            Sau
                        </span>
                    @endif
                </div>
            </nav>
        @endif
    </section>
@endsection