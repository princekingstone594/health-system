<x-app-layout>
    <x-slot name="header">Doctor Leave</x-slot>

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow">

        <form method="POST" action="{{ route('leaves.store') }}">
            @csrf

            <div class="mb-2">
                <input type="date" name="start_date" class="border p-2 w-full" required>
            </div>

            <div class="mb-2">
                <input type="date" name="end_date" class="border p-2 w-full" required>
            </div>

            <div class="mb-2">
                <input type="text" name="reason" placeholder="Reason (optional)" class="border p-2 w-full">
            </div>

            <button class="bg-red-600 text-white px-4 py-2 rounded">
                Add Leave
            </button>
        </form>

        <hr class="my-4">

        @foreach($leaves as $leave)
            <div class="flex justify-between mb-2">
                <span>
                    {{ $leave->start_date }} → {{ $leave->end_date }}
                </span>

                <form method="POST" action="{{ route('leaves.destroy', $leave->id) }}">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-500">Delete</button>
                </form>
            </div>
        @endforeach

    </div>
</x-app-layout>