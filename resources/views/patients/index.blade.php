<x-app-layout>
    <x-slot name="header">
        Patients Management
    </x-slot>

    <div class="max-w-6xl mx-auto">

        <!-- Top Actions -->
        <div class="flex justify-between mb-4">

            <!-- Add Patient -->
            <a href="{{ route('patients.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded">
                + Add Patient
            </a>

            <!-- Search -->
            <form method="GET" action="{{ route('patients.index') }}">
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search patients..."
                       class="border px-3 py-2 rounded">
            </form>

        </div>

        <!-- Table -->
        <div class="bg-white p-6 rounded-xl shadow">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 text-sm border-b">
                        <th>Name</th>
                        <th>Age</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody class="text-sm">
                    @forelse($patients as $patient)
                        <tr class="border-b">

                            <!-- ✅ Name clickable -->
                            <td class="py-2">
                                <a href="{{ route('patients.show', $patient->id) }}"
                                   class="text-blue-600 hover:underline">
                                    {{ $patient->name }}
                                </a>
                            </td>

                            <td>{{ $patient->age }}</td>
                            <td>{{ $patient->status }}</td>

                            <td class="space-x-2">

                                <!-- Edit -->
                                <a href="{{ route('patients.edit', $patient->id) }}"
                                   class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                    Edit
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('patients.destroy', $patient->id) }}"
                                      method="POST"
                                      class="inline">
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
                            <td colspan="4" class="py-2 text-gray-500 text-center">
                                No patients found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $patients->links() }}
            </div>
        </div>
    </div>
</x-app-layout>