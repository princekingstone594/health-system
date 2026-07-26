<x-app-layout>
    <div class="max-w-xl mx-auto p-6">

        <h2 class="text-xl font-bold mb-4">Upload Medical Record</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('records.store') }}" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

            <div class="mb-4">
                <label class="block text-sm">Title</label>
                <input type="text" name="title" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block text-sm">Upload File</label>
                <input type="file" name="file" required>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Upload
            </button>

        </form>
    </div>
</x-app-layout>