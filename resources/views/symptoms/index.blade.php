<x-app-layout>
<div class="max-w-3xl mx-auto p-6">

    <h2 class="text-2xl font-bold mb-4">🧠 AI Symptom Checker</h2>

    <form method="POST" action="{{ route('symptom.analyze') }}">
        @csrf

        <textarea name="symptoms"
            class="w-full border p-3 rounded"
            rows="4"
            placeholder="Describe your symptoms..."></textarea>

        <button class="mt-3 bg-blue-600 text-white px-5 py-2 rounded">
            Analyze
        </button>
    </form>

    @if(session('result'))
        <div class="mt-6 p-4 bg-gray-100 rounded">
            <h3 class="font-bold mb-2">🩺 Result:</h3>
            <pre class="whitespace-pre-wrap">{{ session('result') }}</pre>
        </div>

        <p class="text-sm text-red-500 mt-3">
            ⚠️ This is not a medical diagnosis. Always consult a qualified doctor.
        </p>
    @endif

    @if(!empty($doctors) && count($doctors))
        <div class="mt-6">
            <h3 class="font-bold mb-3"> 🧑‍⚕️ Recommended Doctors</h3>

            @foreach($doctors as $doc)
                <div class="border p-3 mb-2 rounded">
                    <p class="font-bold">{{ $doc->name }}</p>
                    <p>{{ $doc->specialty }}</p>
                    <p class="text-sm text-gray-600">{{ $doc->location }}</p>

                    <a href="{{ route('appointments.create', ['doctor_id' => $doc->id]) }}"
                       class="inline-block mt-2 bg-green-600 text-white px-1 rounded">
                         Book Appointment
                    </a>
                </div>
            @endforeach
        </div>
    @endif

</div>
</x-app-layout>