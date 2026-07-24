<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Admin Dashboard</h2>
    </x-slot>

    <div class="p-6 space-y-6">

        <!-- 📊 Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-gray-500">Total Users</h3>
                <p class="text-2xl font-bold">{{ $totalUsers }}</p>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-gray-500">Doctors</h3>
                <p class="text-2xl font-bold">{{ $totalDoctors }}</p>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-gray-500">Appointments</h3>
                <p class="text-2xl font-bold">{{ $totalAppointments }}</p>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-gray-500">Active Doctors</h3>
                <p class="text-2xl font-bold">{{ $activeDoctors }}</p>
            </div>

        </div>

        <!-- 📈 Chart -->
        <div class="bg-white p-6 rounded shadow">
            <h3 class="mb-4 font-semibold">Appointments (Last 7 Days)</h3>

            <!-- Chart renders below -->
            

        </div>

    </div>
</x-app-layout>