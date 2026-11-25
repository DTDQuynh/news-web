@extends('layouts.admin')

@section('title', 'Quản lý danh mục')
@section('heading', 'Quản lý danh mục')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Danh sách chuyên mục tin tức trên hệ thống.</p>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-plus me-1"></i> Thêm danh mục
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Mô tả</th>
                    <th class="text-center" style="width: 110px;">Số bài viết</th>
                    <th class="text-end" style="width: 150px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td class="fw-semibold">{{ $category->name }}</td>
                        <td><code>{{ $category->slug }}</code></td>
                        <td class="text-muted small">{{ \Illuminate\Support\Str::limit($category->description, 60) ?: '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $category->posts_count }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                Sửa
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Xóa danh mục {{ $category->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Chưa có danh mục nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($categories->hasPages())
    <div class="mt-3">
        {{ $categories->links() }}
    </div>
@endif
@endsection