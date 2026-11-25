@extends('layouts.app')

@php($pageTitle = $category ? 'Tin ' . $category->name : 'Tin tức mới nhất')

@section('title', $pageTitle . ' - ' . config('app.name'))

@section('content')
    <section class="news-home" aria-labelledby="news-list-heading">
        <div class="news-home__heading">
            <h1 id="news-list-heading" class="news-home__title">
                {{ $pageTitle }}
            </h1>
            <span class="news-home__updated">Cập nhật mỗi ngày</span>
        </div>

        @if ($posts->isNotEmpty())
            @php($featuredPost = $posts->first())

            <div class="news-home__layout">
                <div class="news-home__main-column">
                    <x-post-card :post="$featuredPost" :featured="true" :primary="true" />

                    @if ($posts->getCollection()->count() > 4)
                        <section class="news-home__more" aria-label="Các bài viết khác">
                            @foreach ($posts->getCollection()->skip(4) as $post)
                                <x-post-card :post="$post" :featured="true" />
                            @endforeach
                        </section>
                    @endif
                </div>

                <aside class="news-home__sidebar" aria-labelledby="more-news-heading">
                    <h2 id="more-news-heading" class="news-home__sidebar-title">
                        Tin mới
                    </h2>

                    @foreach ($posts->getCollection()->skip(1)->take(3) as $post)
                        <x-post-card :post="$post" />
                    @endforeach

                    @if ($missedPosts->isNotEmpty())
                        <section class="news-home__missed" aria-labelledby="missed-news-heading">
                            <h2 id="missed-news-heading" class="news-home__sidebar-title">
                                Có thể bạn đã bỏ lỡ
                            </h2>

                            @foreach ($missedPosts as $post)
                                <x-post-card :post="$post" />
                            @endforeach
                        </section>
                    @endif
                </aside>
            </div>
        @else
            <p class="news-home__empty">
                {{ $category ? 'Chưa có bài viết nào trong danh mục này.' : 'Chưa có bài viết nào.' }}
            </p>
        @endif

        @if ($posts->hasPages())
            <nav
                class="news-home__pagination"
                aria-label="Phân trang bài viết"
            >
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
                        <a
                            href="{{ $posts->previousPageUrl() }}"
                            rel="prev"
                            class="news-home__pagination-button"
                        >
                            Trước
                        </a>
                    @endif

                    <span class="news-home__pagination-status" aria-current="page">
                        Trang {{ $posts->currentPage() }} / {{ $posts->lastPage() }}
                    </span>

                    @if ($posts->hasMorePages())
                        <a
                            href="{{ $posts->nextPageUrl() }}"
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
