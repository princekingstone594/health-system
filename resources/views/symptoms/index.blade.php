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

</div>
</x-app-layout>