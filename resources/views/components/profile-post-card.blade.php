@props(['post'])

<article class="profile-page__card">
    <span class="news-home__category">{{ $post->category->name }}</span>
    <h3>
        <a href="{{ route('posts.show', ['post' => $post->slug]) }}">
            {{ $post->title }}
        </a>
    </h3>
    @if ($post->summary)
        <p>{{ $post->summary }}</p>
    @endif
    <x-save-post-button :post="$post" />
</article>
