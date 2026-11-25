@props(['post', 'featured' => false, 'primary' => false])

@if ($featured)
    <article @class([
        'news-home__featured',
        'news-home__featured--primary' => $primary,
    ])>
        <div class="news-home__media">
            <a
                href="{{ route('posts.show', ['post' => $post->slug]) }}"
                class="news-home__feature-link"
                aria-label="Đọc bài: {{ $post->title }}"
            >
                @if ($post->image_url)
                    <img
                        src="{{ $post->image_url }}"
                        alt="{{ $post->title }}"
                        class="news-home__image"
                        @if ($primary) fetchpriority="high" @endif
                    >
                @else
                    <div class="news-home__placeholder">
                        <span class="news-home__placeholder-label">
                            {{ $post->category->name }}
                        </span>
                    </div>
                @endif
            </a>
        </div>

        <div class="news-home__feature-copy">
            <span class="news-home__category">
                {{ $post->category->name }}
            </span>
            <h2 class="news-home__feature-title">
                <a
                    href="{{ route('posts.show', ['post' => $post->slug]) }}"
                    class="news-home__article-link"
                >
                    {{ $post->title }}
                </a>
            </h2>

            @if ($post->summary)
                <p class="news-home__feature-summary">
                    {{ $post->summary }}
                </p>
            @endif

            @if ($post->published_at)
                <time class="news-home__feature-date">
                    {{ $post->published_at->format('d/m/Y') }}
                </time>
            @endif
        </div>
    </article>
@else
    <article class="news-home__story">
        <span class="news-home__category">
            {{ $post->category->name }}
        </span>
        <h3 class="news-home__story-title">
            <a
                href="{{ route('posts.show', ['post' => $post->slug]) }}"
                class="news-home__article-link"
            >
                {{ $post->title }}
            </a>
        </h3>

        @if ($post->summary)
            <p class="news-home__story-summary">
                {{ $post->summary }}
            </p>
        @endif

        @if ($post->published_at)
            <time class="news-home__story-date">
                {{ $post->published_at->format('d/m/Y') }}
            </time>
        @endif
    </article>
@endif
