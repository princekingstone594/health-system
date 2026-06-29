<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

```
<!-- Add Patient Button -->
<div class="mb-4">
    <a href="{{ route('patients.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
       + Add Patient
    </a>
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
    </div>
@endif

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500 text-sm">Total Patients</p>
        <h2 class="text-3xl font-bold mt-2">{{ $patientsCount }}</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500 text-sm">Appointments Today</p>
        <h2 class="text-3xl font-bold mt-2">{{ $appointmentsToday }}</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500 text-sm">Doctors Available</p>
        <h2 class="text-3xl font-bold mt-2">{{ $doctorsCount }}</h2>
    </div>

</div>

<!-- Main Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Recent Patients -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow">
        <h3 class="text-lg font-semibold mb-4">Recent Patients</h3>

        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-500 text-sm border-b">
                    <th class="pb-2">Name</th>
                    <th class="pb-2">Age</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($recentPatients as $patient)
                    <tr class="border-b">
                        <td class="py-2">{{ $patient->name }}</td>
                        <td>{{ $patient->age }}</td>
                        <td class="
                            @if($patient->status === 'Stable') text-green-600
                            @elseif($patient->status === 'Critical') text-red-600
                            @else text-yellow-600
                            @endif
                        ">
                            {{ $patient->status }}
                        </td>

                        <!-- ACTIONS -->
                        <td class="py-2">
                            <a href="{{ route('patients.edit', $patient->id) }}"
                               class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                Edit
                            </a>

                            <form action="{{ route('patients.destroy', $patient->id) }}"
                                  method="POST"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <button onclick="return confirm('Delete this patient?')"
                                        class="bg-red-600 text-white px-2 py-1 rounded text-xs">
                                    Delete
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-2 text-gray-500">
                            No patients found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- System Info -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="text-lg font-semibold mb-4">System Info</h3>

        <ul class="space-y-3 text-sm text-gray-600">
            <li>Total Patients: {{ $patientsCount }}</li>
            <li>Doctors: {{ $doctorsCount }}</li>
            <li>Appointments Today: {{ $appointmentsToday }}</li>
        </ul>
    </div>

</div>
```

</x-app-layout>
