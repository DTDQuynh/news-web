@extends('layouts.admin')

@section('title', 'Viết bài mới')
@section('heading', 'Thêm bài viết mới')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                        <input type="text"
                               id="title"
                               name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}"
                               placeholder="Nhập tiêu đề bài viết"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label fw-semibold">Slug (Đường dẫn tĩnh)</label>
                        <input type="text"
                               id="slug"
                               name="slug"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug') }}"
                               placeholder="Để trống sẽ tự tạo từ tiêu đề">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="summary" class="form-label fw-semibold">Tóm tắt</label>
                        <textarea id="summary"
                                  name="summary"
                                  rows="3"
                                  class="form-control @error('summary') is-invalid @enderror"
                                  placeholder="Đoạn mô tả ngắn hiển thị ngoài danh sách tin">{{ old('summary') }}</textarea>
                        @error('summary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label fw-semibold">Nội dung bài viết <span class="text-danger">*</span></label>
                        <textarea id="content"
                                  name="content"
                                  rows="10"
                                  class="form-control @error('content') is-invalid @enderror"
                                  placeholder="Nhập nội dung chi tiết bài viết"
                                  required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                        <select id="category_id"
                                name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror"
                                required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                        <select id="status"
                                name="status"
                                class="form-select @error('status') is-invalid @enderror"
                                required>
                            <option value="published" @selected(old('status', 'published') === 'published')>Xuất bản ngay</option>
                            <option value="draft" @selected(old('status') === 'draft')>Lưu bản nháp</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="thumbnail" class="form-label fw-semibold">Ảnh bìa</label>
                        <input type="file"
                               id="thumbnail"
                               name="thumbnail"
                               accept=".jpg,.jpeg,.png,.webp"
                               class="form-control @error('thumbnail') is-invalid @enderror">
                        <div class="form-text">Định dạng jpg, jpeg, png, webp (tối đa 2MB).</div>
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Lưu bài viết
                        </button>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
                            Hủy
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection