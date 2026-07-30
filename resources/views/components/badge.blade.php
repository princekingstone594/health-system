@props(['type' => 'neutral'])

@php
$classes = match($type) {
    'success' => 'badge-success',
    'warning' => 'badge-warning',
    'danger'  => 'badge-danger',
    'info'    => 'badge-info',
    'ai'      => 'badge-ai',
    'brand'   => 'badge-brand',
    default   => 'badge-neutral',
};
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
