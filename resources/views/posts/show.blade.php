@extends('layouts.app')

@section('title', $post->title . ' - ' . config('app.name'))

@php
    $articleDescription = Str::limit(trim(strip_tags($post->summary ?: $post->content)), 160);
    $articleImage = $post->image_url
        ? (str_starts_with($post->image_url, 'http') ? $post->image_url : url($post->image_url))
        : url('/favicon.png');
    $articleUrl = url()->current();
@endphp

@section('meta')
    <meta name="description" content="{{ $articleDescription }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $post->title }}">
    <meta property="og:description" content="{{ $articleDescription }}">
    <meta property="og:image" content="{{ $articleImage }}">
    <meta property="og:url" content="{{ $articleUrl }}">
@endsection

@section('content')
    <article class="article-page">
        <p class="article-page__category">{{ $post->category->name }}</p>
        <h1 class="article-page__title">{{ $post->title }}</h1>

        @if ($post->summary)
            <p class="article-page__summary">{{ $post->summary }}</p>
        @endif

        <div class="article-page__meta">
            @if ($post->user)
                <span>{{ $post->user->name }}</span>
            @endif

            @if ($post->published_at)
                <time datetime="{{ $post->published_at->toDateString() }}">
                    {{ $post->published_at->format('d/m/Y') }}
                </time>
            @endif

            <span>{{ number_format($post->views_count) }} lượt xem</span>
        </div>

        @if ($post->image_url)
            <figure class="article-page__media">
                <img
                    src="{{ $post->image_url }}"
                    alt="{{ $post->title }}"
                    class="article-page__image"
                >
            </figure>
        @endif

        <div class="article-page__tools" aria-label="Công cụ hỗ trợ đọc">
            <span>Cỡ chữ bài viết</span>
            <button type="button" data-article-font="decrease" aria-label="Giảm cỡ chữ">A-</button>
            <button type="button" data-article-font="increase" aria-label="Tăng cỡ chữ">A+</button>
        </div>

        <div class="article-page__content" data-article-content>{!! $post->content !!}</div>

        <div class="article-page__toolbar">
            <div class="article-page__toolbar-main">
                <a
                    href="{{ route('home') }}"
                    class="article-page__toolbar-back"
                    aria-label="Quay lại tin tức mới nhất"
                    title="Quay lại"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="M19 12H5m7 7-7-7 7-7"></path>
                    </svg>
                </a>
                <x-save-post-button :post="$post" />
            </div>

            <div class="article-page__toolbar-share" aria-label="Chia sẻ bài viết">
                <a
                    class="article-page__share-icon article-page__share-icon--facebook"
                    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($articleUrl) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Chia sẻ lên Facebook"
                    title="Facebook"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="M13.5 21v-8h2.7l.4-3h-3.1V8.1c0-.9.3-1.5 1.6-1.5h1.7V3.9c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3V10H7.3v3h2.8v8h3.4z"></path>
                    </svg>
                </a>
                <button
                    type="button"
                    class="article-page__share-icon"
                    data-copy-link="{{ $articleUrl }}"
                    aria-label="Sao chép liên kết"
                    title="Sao chép liên kết"
                >
                    <img src="{{ asset('images/url-icon.png') }}" alt="" aria-hidden="true">
                </button>
                <a
                    class="article-page__share-icon article-page__share-icon--zalo"
                    href="https://button-share.zalo.me/share?url={{ urlencode($articleUrl) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Chia sẻ lên Zalo"
                    title="Zalo"
                >Zalo</a>
                <span class="article-page__copy-feedback" data-copy-feedback role="status" aria-live="polite"></span>
            </div>
        </div>
        <section
            class="article-page__comments"
            data-comments
            data-comments-url="{{ route('comments.index', ['post' => $post->slug]) }}"
            aria-labelledby="comments-heading"
        >
            <h2 id="comments-heading">Bình luận</h2>

            <form
                class="article-page__comment-form"
                data-comment-form
                data-comment-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
                data-login-url="{{ route('login') }}"
                data-draft-key="comment-draft-{{ $post->id }}"
                action="{{ route('comments.store', ['post' => $post->slug]) }}"
            >
                <label for="comment-content">Ý kiến của bạn (tối đa 500 ký tự)</label>
                <textarea
                    id="comment-content"
                    name="content"
                    maxlength="500"
                    rows="4"
                    required
                    data-comment-input
                ></textarea>
                <div class="article-page__comment-form-footer">
                    <span data-comment-count>0/500</span>
                    <button type="submit">Gửi bình luận</button>
                </div>
                <p data-comment-form-status role="status" aria-live="polite"></p>
            </form>

            <p data-comments-status role="status" aria-live="polite">Đang tải bình luận...</p>
            <div class="article-page__comment-list" data-comments-list></div>
            <nav
                class="article-page__comment-pagination"
                data-comments-pagination
                aria-label="Phân trang bình luận"
                hidden
            >
                <button type="button" data-comments-page="previous">Trước</button>
                <span data-comments-page-label></span>
                <button type="button" data-comments-page="next">Sau</button>
            </nav>
        </section>

        @if ($relatedPosts->isNotEmpty())
            <section class="article-page__related">
                <h2>Bài viết liên quan</h2>
                <div class="article-page__related-grid">
                    @foreach ($relatedPosts as $relatedPost)
                        <article class="article-page__related-card">
                            @if ($relatedPost->image_url)
                                <a href="{{ route('posts.show', ['post' => $relatedPost->slug]) }}">
                                    <img
                                        src="{{ $relatedPost->image_url }}"
                                        alt="{{ $relatedPost->title }}"
                                        loading="lazy"
                                    >
                                </a>
                            @endif
                            <h3>
                                <a href="{{ route('posts.show', ['post' => $relatedPost->slug]) }}">
                                    {{ $relatedPost->title }}
                                </a>
                            </h3>
                            @if ($relatedPost->summary)
                                <p>{{ $relatedPost->summary }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </article>
@endsection

@push('scripts')
    <script>
        (() => {
            const content = document.querySelector('[data-article-content]');
            const fontButtons = document.querySelectorAll('[data-article-font]');
            const fontKey = 'article-font-size';
            const minimumSize = 16;
            const maximumSize = 24;
            let fontSize = Number.parseInt(localStorage.getItem(fontKey), 10);

            if (!Number.isFinite(fontSize)) {
                fontSize = 18;
            }

            const applyFontSize = () => {
                if (content) {
                    content.style.fontSize = `${fontSize}px`;
                }
                localStorage.setItem(fontKey, String(fontSize));
            };

            applyFontSize();

            fontButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const action = button.dataset.articleFont;
                    fontSize += action === 'increase' ? 1 : -1;
                    fontSize = Math.min(maximumSize, Math.max(minimumSize, fontSize));
                    applyFontSize();
                });
            });

            document.querySelectorAll('[data-copy-link]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const url = button.dataset.copyLink;

                    try {
                        await navigator.clipboard.writeText(url);
                    } catch {
                        const textarea = document.createElement('textarea');
                        textarea.value = url;
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand('copy');
                        textarea.remove();
                    }

                    const feedback = button.parentElement.querySelector('[data-copy-feedback]');
                    if (feedback) {
                        feedback.textContent = 'Đã sao chép!';
                    }

                    window.setTimeout(() => {
                        if (feedback) {
                            feedback.textContent = '';
                        }
                    }, 2000);
                });
            });
        })();
    </script>
@endpush
