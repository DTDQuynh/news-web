@extends('layouts.app')

@section('title', $post->title . ' - ' . config('app.name'))

@section('content')
    <article class="article-page">
        <a href="{{ route('home') }}" class="article-page__back">
            Tin tức mới nhất
        </a>

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

        <div class="article-page__content">{{ $post->content }}</div>
    </article>
@endsection
