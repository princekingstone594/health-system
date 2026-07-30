<![CDATA[<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="page-title">Admin Dashboard</span>
                <p class="page-subtitle">System overview and analytics</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.revenue') }}" class="btn-secondary">
                    <x-icon name="credit-card" class="w-4 h-4" />
                    Revenue
                </a>
                <a href="{{ route('plans') }}" class="btn-primary">
                    <x-icon name="building" class="w-4 h-4" />
                    Plans
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <x-stat-card label="Total Users" :value="$totalUsers" icon="users" color="brand" />
            <x-stat-card label="Doctors" :value="$totalDoctors" icon="user" color="violet" />
            <x-stat-card label="Appointments" :value="$totalAppointments" icon="calendar" color="sky" />
            <x-stat-card label="Active Doctors" :value="$activeDoctors" icon="check" color="emerald" />
        </div>

        {{-- Chart --}}
        <div class="card">
            <div class="card-header">
                <div class="flex items-center gap-2">
                    <x-icon name="chart" class="w-5 h-5 text-brand-600" />
                    <h3 class="section-title">Appointments (Last 7 Days)</h3>
                </div>
                <span class="badge-brand">Weekly</span>
            </div>
            <div class="card-body">
                {{-- Simple CSS bar chart --}}
                <div class="flex items-end justify-between gap-3 h-48 pt-4">
                    @php
                        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        $maxVal = 100;
                    @endphp
                    @foreach($days as $i => $day)
                        @php $height = 30 + (($i * 13) % 70); @endphp
                        <div class="flex flex-1 flex-col items-center gap-2">
                            <div class="w-full rounded-t-lg bg-gradient-brand transition-all duration-500 hover:opacity-80" style="height: {{ $height }}%"></div>
                            <span class="text-xs text-slate-500">{{ $day }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <a href="{{ route('patients.index') }}" class="card-hover p-6 group">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 group-hover:bg-brand-100 transition-colors">
                        <x-icon name="users" class="w-6 h-6 text-brand-600" />
                    </div>
                    <div>
                        <h3 class="section-title">Manage Patients</h3>
                        <p class="text-sm text-slate-500">View and manage patient records</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.revenue') }}" class="card-hover p-6 group">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 group-hover:bg-emerald-100 transition-colors">
                        <x-icon name="credit-card" class="w-6 h-6 text-emerald-600" />
                    </div>
                    <div>
                        <h3 class="section-title">Revenue Reports</h3>
                        <p class="text-sm text-slate-500">Track subscription revenue</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('plans') }}" class="card-hover p-6 group">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 group-hover:bg-violet-100 transition-colors">
                        <x-icon name="building" class="w-6 h-6 text-violet-600" />
                    </div>
                    <div>
                        <h3 class="section-title">Subscription Plans</h3>
                        <p class="text-sm text-slate-500">Manage pricing tiers</p>
                    </div>
                </div>
            </a>
        </div>

    </div>
</x-app-layout>
]]>