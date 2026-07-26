<x-app-layout>
    <div class="p-6">

        <h2 class="text-xl font-bold mb-4">Patient Records</h2>

        @forelse($records as $record)
            <div class="bg-white p-4 rounded shadow mb-3 flex justify-between">

                <div>
                    <p class="font-semibold">{{ $record->title ?? 'Untitled' }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $record->created_at->format('Y-m-d') }}
                    </p>
                </div>

                <a href="{{ route('records.download', $record->id) }}"
                   class="text-blue-600 underline">
                    Download
                </a>

            </div>
        @empty
            <p>No records available.</p>
        @endforelse

    </div>
</x-app-layout>