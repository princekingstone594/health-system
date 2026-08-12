<![CDATA[<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MedFlow') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
    <script src="{{ asset('js/app.js') }}"></script>
</head>

<body class="font-sans antialiased">

<div class="flex min-h-screen">

    {{-- Left panel (branding) --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-2/5 flex-col justify-between bg-slate-900 p-12 relative overflow-hidden">
        {{-- Decorative blobs --}}
        <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-brand-600/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-brand-400/10 blur-3xl"></div>
        <div class="absolute top-1/3 left-1/4 h-64 w-64 rounded-full bg-violet-500/10 blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 h-48 w-48 rounded-full bg-sky-500/10 blur-3xl"></div>

        {{-- Grid pattern --}}
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(white 1px, transparent 1px), linear-gradient(90deg, white 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="relative">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-brand shadow-lg shadow-brand-500/30">
                    <x-icon name="stethoscope" class="w-6 h-6 text-white" />
                </div>
                <span class="text-xl font-bold text-white">{{ config('app.name', 'MedFlow') }}</span>
            </div>
        </div>

        <div class="relative space-y-8">
            <blockquote class="text-3xl font-bold text-white leading-snug">
                "Healthcare made simple —
                <span class="gradient-text bg-gradient-to-r from-brand-300 to-brand-500 bg-clip-text text-transparent">for doctors, patients, and clinics.</span>"
            </blockquote>

            {{-- Feature list --}}
            <div class="space-y-4">
                @foreach([
                    ['icon' => 'sparkles', 'label' => 'AI-powered symptom checker'],
                    ['icon' => 'calendar', 'label' => 'Smart appointment scheduling'],
                    ['icon' => 'bell', 'label' => 'Automated follow-ups'],
                    ['icon' => 'shield', 'label' => 'HIPAA-grade security'],
                ] as $feature)
                    <div class="flex items-center gap-3 text-slate-300">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-500/20">
                            <x-icon :name="$feature['icon']" class="w-3.5 h-3.5 text-brand-400" />
                        </div>
                        <span class="text-sm">{{ $feature['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center gap-4">
                <div class="flex -space-x-2">
                    @foreach(['JD', 'SK', 'AM', 'RP'] as $initials)
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-brand text-xs font-bold text-white ring-2 ring-slate-900">{{ $initials }}</div>
                    @endforeach
                </div>
                <p class="text-sm text-slate-400">Trusted by 500+ healthcare professionals</p>
            </div>
        </div>

        <p class="relative text-xs text-slate-500">&copy; {{ date('Y') }} {{ config('app.name', 'MedFlow') }}. All rights reserved.</p>
    </div>

    {{-- Right panel (form) --}}
    <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 bg-surface-muted relative overflow-hidden">

        {{-- Decorative gradients --}}
        <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-brand-100/40 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-48 w-48 rounded-full bg-violet-100/30 blur-3xl"></div>

        {{-- Mobile logo --}}
        <div class="lg:hidden flex items-center gap-2 mb-8 relative">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-brand shadow-lg shadow-brand-500/30">
                <x-icon name="stethoscope" class="w-5 h-5 text-white" />
            </div>
            <span class="text-lg font-bold text-slate-900">{{ config('app.name', 'MedFlow') }}</span>
        </div>

        <div class="w-full max-w-md relative animate-fade-in-up">
            <div class="card p-8 shadow-card-hover">
                {{ $slot }}
            </div>
        </div>

    </div>

</div>

</body>
</html>
]]>