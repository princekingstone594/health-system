@props([
    'title',
    'description' => null,
    'icon' => 'document',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-16 px-6 text-center']) }}>
    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 mb-4">
        <x-icon :name="$icon" class="w-7 h-7 text-slate-400" />
    </div>
    <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
    @if($description)
        <p class="text-sm text-slate-500 mt-1 max-w-sm">{{ $description }}</p>
    @endif
    @if($slot->isNotEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
