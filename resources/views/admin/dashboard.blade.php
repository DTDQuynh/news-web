@extends('layouts.admin')

@section('title', 'Tổng quan')
@section('heading', 'Tổng quan hệ thống')

@section('content')
<div class="row g-3">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Tổng bài viết</div>
                <div class="fs-3 fw-bold">{{ number_format($totalPosts, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Tổng danh mục</div>
                <div class="fs-3 fw-bold">{{ number_format($totalCategories, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.comments.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-reset">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Tổng bình luận</div>
                <div class="fs-3 fw-bold">{{ number_format($totalComments, 0, ',', '.') }}</div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Tổng lượt xem</div>
                <div class="fs-3 fw-bold">{{ number_format($totalViews, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
