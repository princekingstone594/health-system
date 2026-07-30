@props(['type' => 'success'])

@php
$classes = match($type) {
    'error' => 'alert-error',
    'info'  => 'alert-info',
    default => 'alert-success',
};
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    {{ $slot }}
</div>
