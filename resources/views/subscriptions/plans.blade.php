<![CDATA[<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <span class="page-title">Choose Your Plan</span>
            <p class="page-subtitle mt-1">Select the perfect plan for your practice</p>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto">

        {{-- Pricing cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">

            {{-- Basic --}}
            <div class="pricing-card">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                        <x-icon name="building" class="w-5 h-5 text-slate-600" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Basic</h3>
                </div>

                <div class="flex items-baseline gap-1">
                    <span class="text-4xl font-bold tracking-tight text-slate-900">$10</span>
                    <span class="text-sm text-slate-500">/mo</span>
                </div>

                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        Up to 100 patients
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        Basic appointment scheduling
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        Email notifications
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        7-day support
                    </li>
                </ul>

                <a href="{{ route('subscribe', 'basic') }}" class="btn-secondary w-full">Subscribe</a>
            </div>

            {{-- Pro (Featured) --}}
            <div class="pricing-card-featured">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                    <span class="badge-brand bg-brand-600 text-white px-3 py-1">Most Popular</span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100">
                        <x-icon name="sparkles" class="w-5 h-5 text-brand-600" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Pro</h3>
                </div>

                <div class="flex items-baseline gap-1">
                    <span class="text-4xl font-bold tracking-tight text-brand-700">$25</span>
                    <span class="text-sm text-slate-500">/mo</span>
                </div>

                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        Up to 1,000 patients
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        Advanced scheduling & calendar
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        AI symptom checker
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        AI follow-ups
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        Priority support
                    </li>
                </ul>

                <a href="{{ route('subscribe', 'pro') }}" class="btn-primary w-full">Subscribe</a>
            </div>

            {{-- Enterprise --}}
            <div class="pricing-card">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100">
                        <x-icon name="building" class="w-5 h-5 text-violet-600" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Enterprise</h3>
                </div>

                <div class="flex items-baseline gap-1">
                    <span class="text-4xl font-bold tracking-tight text-slate-900">$50</span>
                    <span class="text-sm text-slate-500">/mo</span>
                </div>

                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        Unlimited patients
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        Multi-clinic support
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        Custom AI integrations
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        Dedicated account manager
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        24/7 premium support
                    </li>
                </ul>

                <a href="{{ route('subscribe', 'enterprise') }}" class="btn-secondary w-full">Subscribe</a>
            </div>

        </div>

        {{-- Trust badges --}}
        <div class="mt-12 flex flex-wrap items-center justify-center gap-8 text-sm text-slate-400">
            <div class="flex items-center gap-2">
                <x-icon name="check" class="w-4 h-4 text-brand-600" />
                No setup fees
            </div>
            <div class="flex items-center gap-2">
                <x-icon name="check" class="w-4 h-4 text-brand-600" />
                Cancel anytime
            </div>
            <div class="flex items-center gap-2">
                <x-icon name="check" class="w-4 h-4 text-brand-600" />
                Secure payments
            </div>
            <div class="flex items-center gap-2">
                <x-icon name="check" class="w-4 h-4 text-brand-600" />
                HIPAA compliant
            </div>
        </div>

    </div>
</x-app-layout>
]]>