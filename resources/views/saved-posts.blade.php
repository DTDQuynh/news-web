@extends('layouts.app')

@section('title', 'Bài viết đã lưu - ' . config('app.name'))

@section('content')
    <div class="profile-page">
        <h1 id="saved-posts-heading" class="profile-page__title">Bài viết đã lưu</h1>

        @if (session('status'))
            <p class="profile-page__status" role="status">{{ session('status') }}</p>
        @endif

        <section id="saved-posts" class="profile-page__section" aria-labelledby="saved-posts-heading">
            @if ($savedPosts->isEmpty())
                <p>Bạn chưa lưu bài viết nào.</p>
            @else
                <div class="profile-page__grid">
                    @foreach ($savedPosts as $post)
                        <x-profile-post-card :post="$post" />
                    @endforeach
                </div>

                {{ $savedPosts->links() }}
            @endif
        </section>
    </div>
@endsection
