@props([
    'label',
    'value',
    'icon' => 'chart',
    'color' => 'brand',
    'trend' => null,
    'trendDirection' => 'up',
])

@php
$colorClasses = [
    'brand'   => ['bg' => 'bg-brand-50', 'icon' => 'text-brand-600', 'value' => 'text-brand-700', 'glow' => 'shadow-glow-brand'],
    'emerald' => ['bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'value' => 'text-emerald-700', 'glow' => 'shadow-glow-emerald'],
    'sky'     => ['bg' => 'bg-sky-50', 'icon' => 'text-sky-600', 'value' => 'text-sky-700', 'glow' => 'shadow-glow-sky'],
    'violet'  => ['bg' => 'bg-violet-50', 'icon' => 'text-violet-600', 'value' => 'text-violet-700', 'glow' => 'shadow-glow-violet'],
    'amber'   => ['bg' => 'bg-amber-50', 'icon' => 'text-amber-600', 'value' => 'text-amber-700', 'glow' => 'shadow-glow-amber'],
];
$c = $colorClasses[$color] ?? $colorClasses['brand'];
@endphp

<div {{ $attributes->merge(['class' => 'stat-card animate-fade-in-up']) }}>
    <div class="flex items-start justify-between">
        <div class="stat-icon {{ $c['bg'] }} {{ $c['glow'] }}">
            <x-icon :name="$icon" class="w-5 h-5 {{ $c['icon'] }}" />
        </div>
        @if($trend)
            <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $trendDirection === 'up' ? 'text-emerald-600' : 'text-red-500' }}">
                <x-icon :name="$trendDirection === 'up' ? 'arrow-up-right' : 'arrow-down'" class="w-3 h-3" />
                {{ $trend }}
            </span>
        @endif
    </div>
    <div>
        <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
        <p class="text-3xl font-bold tracking-tight {{ $c['value'] }} mt-1">{{ $value }}</p>
    </div>
</div>