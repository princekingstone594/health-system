<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <h2 class="text-2xl font-bold mb-4">
            Dr. {{ $doctor->name }}
        </h2>

        {{-- ⭐ Average Rating --}}
        <p class="mb-4">
            ⭐ Average Rating:
            {{ round($doctor->reviewsReceived->avg('rating'), 1) ?? 'No ratings' }}
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

        @if(isset($appointment) && $appointment->array_intersect-summary)
          <div class="mt-4 p-4 bg-blue-50 border rounded">
            <h3 class="font-bold mb-2">🧠 AI Patient Summary</h3>
            <p class="whitespace-pre-line-line text-sm">
                {{ $appointment->ai_summary }}
            </p>
        @endif

    </div>
</x-app-layout>