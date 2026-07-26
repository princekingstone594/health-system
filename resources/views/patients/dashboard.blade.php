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

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded">
                {{ session('error') }}
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

        {{-- 📅 Upcoming Appointments --}}
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

                        {{-- Status --}}
                        <span class="px-2 py-1 text-xs rounded
                            @if($appt->status == 'approved') bg-green-100 text-green-700
                            @elseif($appt->status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($appt->status == 'cancelled') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700
                            @endif
                        ">
                            {{ ucfirst($appt->status) }}
                        </span>

                        {{-- Payment --}}
                        @if($appt->is_paid)
                            <span class="text-green-600 text-sm font-semibold">Paid</span>
                        @else
                            <a href="{{ route('checkout', $appt->id) }}"
                               class="text-blue-600 text-sm underline">
                                Pay
                            </a>
                        @endif

                        {{-- Actions --}}
                        @if($appt->status !== 'cancelled' && $appt->status !== 'completed')
                            <div class="flex gap-2 mt-1">

                                <form method="POST" action="{{ route('appointments.cancel', $appt->id) }}">
                                    @csrf
                                    <button
                                        onclick="return confirm('Cancel this appointment?')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                        Cancel
                                    </button>
                                </form>

                                <a href="{{ route('appointments.reschedule.form', $appt->id) }}"
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                    Reschedule
                                </a>

                            </div>
                        @endif

                    </div>
                </div>
            @empty
                <p class="text-gray-500">No upcoming appointments.</p>
            @endforelse
        </div>

        {{-- 🕘 Recent Appointments + Reviews --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Recent History</h3>

            @forelse($pastAppointments as $appt)
                <div class="border-b py-4">

                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold">
                                Dr. {{ $appt->doctor->name ?? 'Doctor' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $appt->date }} at {{ $appt->time }}
                            </p>
                        </div>

                        <span class="text-sm capitalize
                            @if($appt->status == 'cancelled') text-red-500
                            @elseif($appt->status == 'completed') text-green-500
                            @else text-gray-600
                            @endif
                        ">
                            {{ $appt->status }}
                        </span>
                    </div>

                    {{-- ⭐ REVIEW FORM --}}
                    @php
                        $alreadyReviewed = \App\Models\Review::where('appointment_id', $appt->id)->exists();
                    @endphp

                    @if($appt->status === 'approved' && now()->gt($appt->date) && !$alreadyReviewed)
                        <form method="POST" action="{{ route('reviews.store') }}" class="mt-3 flex items-center gap-2">
                            @csrf

                            <input type="hidden" name="appointment_id" value="{{ $appt->id }}">

                            <select name="rating" class="border p-1 rounded" required>
                                <option value="">Rate</option>
                                <option value="5">⭐⭐⭐⭐⭐</option>
                                <option value="4">⭐⭐⭐⭐</option>
                                <option value="3">⭐⭐⭐</option>
                                <option value="2">⭐⭐</option>
                                <option value="1">⭐</option>
                            </select>

                            <input type="text" name="comment"
                                   placeholder="Write a review..."
                                   class="border p-1 rounded w-64">

                            <button class="bg-green-600 text-white px-3 py-1 rounded text-sm">
                                Submit
                            </button>
                        </form>
                    @elseif($alreadyReviewed)
                        <p class="text-green-600 text-sm mt-2">✅ You already reviewed this appointment</p>
                    @endif

                </div>
            @empty
                <p class="text-gray-500">No past appointments.</p>
            @endforelse
        </div>

        {{-- 🚀 Action --}}
        <div class="flex justify-end">
            <a href="{{ route('booking.index') }}"
               class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
                + Book Appointment
            </a>
        </div>

    </div>
</x-app-layout>