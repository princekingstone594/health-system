@props([
    'title',
    'description' => null,
    'icon' => 'document',
    'color' => 'brand',
])

@php
$colorClasses = [
    'brand'   => ['bg' => 'bg-brand-50', 'icon' => 'text-brand-600'],
    'slate'   => ['bg' => 'bg-slate-100', 'icon' => 'text-slate-400'],
    'violet'  => ['bg' => 'bg-violet-50', 'icon' => 'text-violet-600'],
    'amber'   => ['bg' => 'bg-amber-50', 'icon' => 'text-amber-600'],
];
$c = $colorClasses[$color] ?? $colorClasses['brand'];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-16 px-6 text-center animate-fade-in']) }}>
    <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $c['bg'] }} mb-4 shadow-sm">
        <x-icon :name="$icon" class="w-7 h-7 {{ $c['icon'] }}" />
    </div>
    <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
    @if($description)
        <p class="text-sm text-slate-500 mt-1 max-w-sm">{{ $description }}</p>
    @endif
    @if($slot->isNotEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>