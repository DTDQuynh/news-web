@extends('layouts.admin')

@section('title', 'Quản lý bài viết')
@section('heading', 'Quản lý bài viết')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <p class="text-muted mb-0">Danh sách toàn bộ tin tức trên hệ thống.</p>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <form action="{{ route('admin.posts.fetch-rss') }}"
              method="POST"
              class="m-0"
              onsubmit="const btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin me-1\'></i> Đang cập nhật...';">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-rss me-1"></i> Cập nhật nguồn cấp tin tức tự động
            </button>
        </form>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Viết bài mới
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th style="width: 80px;">Ảnh</th>
                    <th>Tiêu đề</th>
                    <th style="width: 140px;">Danh mục</th>
                    <th style="width: 130px;">Tác giả</th>
                    <th class="text-center" style="width: 110px;">Trạng thái</th>
                    <th class="text-center" style="width: 90px;">Lượt xem</th>
                    <th class="text-end" style="width: 140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td>{{ $post->id }}</td>
                        <td>
                            @if ($post->image_url)
                                <img src="{{ $post->image_url }}"
                                     alt="{{ $post->title }}"
                                     class="rounded object-fit-cover"
                                     style="width: 60px; height: 42px;">
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($post->title, 55) }}</div>
                            <div class="text-muted small">
                                {{ $post->published_at?->format('d/m/Y H:i') ?? $post->created_at->format('d/m/Y H:i') }}
                                @if ($post->rss_source)
                                    <span class="badge bg-info-subtle text-info-emphasis ms-1">{{ $post->rss_source }}</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $post->category->name ?? '—' }}</td>
                        <td class="small">{{ $post->user->name ?? 'Hệ thống' }}</td>
                        <td class="text-center">
                            @if ($post->is_published)
                                <span class="badge bg-success">Xuất bản</span>
                            @else
                                <span class="badge bg-secondary">Bản nháp</span>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($post->views_count, 0, ',', '.') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-primary">
                                Sửa
                            </a>
                            <form action="{{ route('admin.posts.destroy', $post) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Xóa bài viết này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Chưa có bài viết nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($posts->hasPages())
    <div class="mt-3">
        {{ $posts->links() }}
    </div>
@endif
@endsection