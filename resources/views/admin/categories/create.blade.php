@extends('layouts.admin')

@section('title', 'Thêm danh mục')
@section('heading', 'Thêm danh mục mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Ví dụ: Công nghệ"
                               required>
                        @error('name')
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
                               placeholder="Để trống sẽ tự tạo từ tên danh mục">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Mô tả</label>
                        <textarea id="description"
                                  name="description"
                                  rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Mô tả ngắn gọn về chuyên mục này">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Lưu danh mục
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                            Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection