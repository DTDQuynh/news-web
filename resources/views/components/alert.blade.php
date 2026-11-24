@props(['type' => 'info'])

@php
    $alertStyles = [
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error' => 'border-rose-200 bg-rose-50 text-rose-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info' => 'border-blue-200 bg-blue-50 text-blue-800',
    ];
    $style = $alertStyles[$type] ?? $alertStyles['info'];
@endphp

<div
    {{ $attributes->class(['rounded border px-3 py-2 text-sm', $style]) }}
    role="{{ $type === 'error' ? 'alert' : 'status' }}"
>
    {{ $slot }}
</div>
