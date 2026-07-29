<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Patient Dashboard
        </h2>
    </x-slot>

    <div class="p-6 space-y-6">

        {{-- 🔔 Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- 📊 Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div class="bg-white p-5 rounded shadow">
                <p class="text-gray-500 text-sm">Total Appointments</p>
                <h3 class="text-2xl font-bold">{{ $totalAppointments }}</h3>
            </div>

            <div class="bg-white p-5 rounded shadow">
                <p class="text-gray-500 text-sm">Upcoming</p>
                <h3 class="text-2xl font-bold text-blue-600">{{ $upcomingCount }}</h3>
            </div>

            <div class="bg-white p-5 rounded shadow">
                <p class="text-gray-500 text-sm">Completed</p>
                <h3 class="text-2xl font-bold text-green-600">
                    {{ $totalAppointments - $upcomingCount }}
                </h3>
            </div>

        </div>

        {{-- 📅 Upcoming --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Upcoming Appointments</h3>

            @forelse($upcomingAppointments as $appt)
                <div class="flex justify-between items-center border-b py-4">

                    <div>
                        <p class="font-semibold">
                            Dr. {{ $appt->doctor->name ?? 'Doctor' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $appt->date }} at {{ $appt->time }}
                        </p>
                    </div>

                    <div class="flex flex-col items-end gap-2">

                        <span class="text-xs px-2 py-1 rounded bg-gray-100">
                            {{ ucfirst($appt->status) }}
                        </span>

                        {{-- 💳 Payment --}}
                        @if($appt->is_paid)
                            <span class="text-green-600 text-sm font-semibold">Paid</span>
                        @else
                            <a href="{{ route('checkout', $appt->id) }}"
                               class="text-blue-600 text-sm underline">
                                Pay
                            </a>
                        @endif

                    </div>
                </div>
            @empty
                <p>No upcoming appointments.</p>
            @endforelse
        </div>

        {{-- 🕘 HISTORY + MEDICAL --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Medical History</h3>

            @forelse($pastAppointments as $appt)
                <div class="border-b py-4 space-y-2">

                    <p class="font-semibold">
                        Dr. {{ $appt->doctor->name ?? 'Doctor' }}
                    </p>

                    <p class="text-sm text-gray-500">
                        {{ $appt->date }} at {{ $appt->time }}
                    </p>

                    {{-- 📝 Diagnosis --}}
                    @if($appt->is_shared_with_patient && $appt->diagnosis)
                        <div class="bg-gray-50 p-3 rounded">
                            <p class="text-sm"><strong>Diagnosis:</strong> {{ $appt->diagnosis }}</p>
                        </div>
                    @endif

                    {{-- 💊 Prescription --}}
                    @if($appt->is_shared_with_patient && $appt->prescription)
                        <div class="bg-gray-50 p-3 rounded">
                            <p class="text-sm"><strong>Prescription:</strong> {{ $appt->prescription }}</p>
                        </div>

                        <a href="{{ route('prescription.download', $appt->id) }}"
                           class="text-blue-600 text-sm underline">
                            📄 Download Prescription
                        </a>
                    @endif

                </div>
            @empty
                <p>No history yet.</p>
            @endforelse
        </div>

        {{-- 🤖 AI FOLLOW-UPS --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold text-purple-700 mb-4">
                🤖 AI Follow-Ups
            </h3>

            @forelse($followUps as $follow)
                <div class="border-b py-3">
                    <p class="text-sm text-gray-700">
                        {{ $follow->message }}
                    </p>

                    <p class="text-xs text-gray-400">
                        {{ $follow->created_at->diffForHumans() }}
                    </p>
                </div>
            @empty
                <p class="text-gray-500">No follow-ups yet.</p>
            @endforelse
        </div>

    </div>
</x-app-layout>