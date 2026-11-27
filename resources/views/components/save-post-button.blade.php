@props(['post'])

@php($isSaved = auth()->check() && auth()->user()->savedPosts->contains('id', $post->id))

@auth
    <form action="{{ route('saved-posts.toggle', ['post' => $post->slug]) }}" method="POST" class="bookmark-action">
        @csrf
        <button type="submit" aria-pressed="{{ $isSaved ? 'true' : 'false' }}">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M6 3.5h12v17l-6-4-6 4z"></path>
            </svg>
            <span>{{ $isSaved ? 'Đã lưu' : 'Lưu' }}</span>
        </button>
    </form>
@else
    <a
        href="{{ route('login') }}"
        class="bookmark-action"
        data-login-modal-open
        aria-haspopup="dialog"
    >
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path d="M6 3.5h12v17l-6-4-6 4z"></path>
        </svg>
        <span>Lưu</span>
    </a>
@endauth
