<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <h2 class="text-2xl font-bold mb-4">
            Dr. {{ $doctor->name }}
        </h2>

        {{-- ⭐ Average Rating --}}
        <p class="mb-4">
            ⭐ Average Rating:
            {{ $doctor->reviewsReceived->count() 
                ? round($doctor->reviewsReceived->avg('rating'), 1) 
                : 'No ratings' }}
        </p>

        {{-- 📝 Reviews --}}
        <h3 class="text-xl font-semibold mt-6">Reviews</h3>

        @forelse($doctor->reviewsReceived as $review)
            <div class="border p-3 mt-2 rounded">
                <p>⭐ {{ $review->rating }}/5</p>
                <p>{{ $review->comment }}</p>
                <small class="text-gray-500">
                    By: {{ $review->patient->name }}
                </small>
            </div>
        @empty
            <p class="text-gray-500 mt-2">No reviews yet.</p>
        @endforelse

        {{-- 🧠 AI Summary --}}
        @if(isset($appointment) && $appointment->ai_summary)
            <div class="mt-4 p-4 bg-blue-50 border rounded">
                <h3 class="font-bold mb-2">🧠 AI Patient Summary</h3>
                <p class="whitespace-pre-line text-sm">
                    {{ $appointment->ai_summary }}
                </p>
            </div>
        @endif

        {{-- 🧾 Doctor Form --}}
        @if(auth()->user()->role === 'doctor' && isset($appointment))

            <div class="mt-6 p-4 border rounded bg-gray-50">

                <h3 class="text-lg font-bold mb-3">🧾 Doctor Medical Entry</h3>

                {{-- Success --}}
                @if(session('success'))
                    <div class="mb-3 p-2 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('appointments.medical.update', $appointment->id) }}">
                    @csrf

                    {{-- Doctor Notes --}}
                    <div class="mb-3">
                        <label class="block font-semibold">Doctor Notes</label>
                        <textarea name="doctor_notes" rows="3" class="w-full border rounded p-2">{{ old('doctor_notes', $appointment->doctor_notes) }}</textarea>
                    </div>

                    {{-- Diagnosis --}}
                    <div class="mb-3">
                        <label class="block font-semibold">Diagnosis</label>
                        <textarea name="diagnosis" rows="2" class="w-full border rounded p-2">{{ old('diagnosis', $appointment->diagnosis) }}</textarea>
                    </div>

                    {{-- Prescription --}}
                    <div class="mb-3">
                        <label class="block font-semibold">Prescription</label>
                        <textarea name="prescription" rows="2" class="w-full border rounded p-2">{{ old('prescription', $appointment->prescription) }}</textarea>
                    </div>

                    {{-- Errors --}}
                    @if($errors->any())
                        <div class="mb-3 text-red-500 text-sm">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Save Medical Record
                    </button>
                </form>

            </div>

        @endif

    </div>
</x-app-layout>