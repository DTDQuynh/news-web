@extends('layouts.app')

@section('title', 'Đăng nhập - ' . config('app.name'))

@section('content')
    <div class="mx-auto max-w-md px-4 py-12">
        <div class="rounded border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @include('partials.login-panel')
        </div>
    </div>
@endsection
