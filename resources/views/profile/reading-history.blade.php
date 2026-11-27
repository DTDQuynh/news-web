@extends('layouts.app')

@section('title', 'Lịch sử đã đọc - ' . config('app.name'))

@section('content')
    <div class="profile-page">
        <h1 id="reading-history-heading" class="profile-page__title">Lịch sử đã đọc gần đây</h1>

        <section id="reading-history" class="profile-page__section" aria-labelledby="reading-history-heading">
            @if ($recentPosts->isEmpty())
                <p>Chưa có bài viết nào trong lịch sử đọc của phiên này.</p>
            @else
                <div class="profile-page__grid">
                    @foreach ($recentPosts as $post)
                        <x-profile-post-card :post="$post" />
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
