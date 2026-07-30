@props(['href', 'active' => false, 'icon' => 'home'])

@php
$classes = $active ? 'nav-item-active' : 'nav-item';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    <x-icon :name="$icon" class="w-5 h-5 shrink-0" />
    <span>{{ $slot }}</span>
</a>
