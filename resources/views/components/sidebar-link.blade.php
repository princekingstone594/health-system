@props(['href', 'active' => false, 'icon' => 'home'])

@php
$classes = $active ? 'nav-item-active' : 'nav-item';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    <x-icon :name="$icon" class="w-5 h-5 shrink-0" />
    <span class="truncate">{{ $slot }}</span>
    @if($active)
        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-400 animate-pulse-soft"></span>
    @endif
</a>