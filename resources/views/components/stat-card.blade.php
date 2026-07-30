@props([
    'label',
    'value',
    'icon' => 'chart',
    'color' => 'brand',
    'trend' => null,
])

@php
$colorClasses = [
    'brand'   => ['bg' => 'bg-brand-50', 'icon' => 'text-brand-600', 'value' => 'text-brand-700'],
    'emerald' => ['bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'value' => 'text-emerald-700'],
    'sky'     => ['bg' => 'bg-sky-50', 'icon' => 'text-sky-600', 'value' => 'text-sky-700'],
    'violet'  => ['bg' => 'bg-violet-50', 'icon' => 'text-violet-600', 'value' => 'text-violet-700'],
    'amber'   => ['bg' => 'bg-amber-50', 'icon' => 'text-amber-600', 'value' => 'text-amber-700'],
];
$c = $colorClasses[$color] ?? $colorClasses['brand'];
@endphp

<div {{ $attributes->merge(['class' => 'stat-card']) }}>
    <div class="flex items-start justify-between">
        <div class="stat-icon {{ $c['bg'] }}">
            <x-icon :name="$icon" class="w-5 h-5 {{ $c['icon'] }}" />
        </div>
        @if($trend)
            <span class="text-xs font-medium text-emerald-600">{{ $trend }}</span>
        @endif
    </div>
    <div>
        <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
        <p class="text-3xl font-bold tracking-tight {{ $c['value'] }} mt-1">{{ $value }}</p>
    </div>
</div>
