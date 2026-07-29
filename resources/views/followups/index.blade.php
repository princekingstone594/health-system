<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <h2 class="text-2xl font-bold mb-6">💬 AI Follow-Ups</h2>

        @forelse($followUps as $f)
            <div class="border p-5 mb-5 rounded-lg shadow-sm bg-gray-50">

                {{-- AI MESSAGE --}}
                <div class="mb-3">
                    <p class="text-gray-700">
                        <strong>🤖 AI Check-in:</strong><br>
                        {{ $f->message }}
                    </p>

                    {{-- STEP 5: STATUS BADGE --}}
                    <div class="mt-2">
                        @if($f->response)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">
                                ✅ Responded
                            </span>
                        @else
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded">
                                ⏳ Awaiting Response
                            </span>
                        @endif
                    </div>
                </div>

                {{-- PATIENT RESPONSE --}}
                @if($f->response)
                    <div class="mt-3 p-3 bg-green-50 border rounded">
                        <p class="text-green-700">
                            <strong>🧑 Your Response:</strong><br>
                            {{ $f->response }}
                        </p>
                    </div>
                @else
                    {{-- RESPONSE FORM --}}
                    <form method="POST" action="{{ route('followups.reply', $f->id) }}">
                        @csrf

                        <textarea 
                            name="response"
                            rows="3"
                            class="w-full border rounded p-2 mb-2 focus:ring focus:ring-blue-200"
                            placeholder="Describe how you're feeling..."></textarea>

                        <button 
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                            Send Response
                        </button>
                    </form>
                @endif

                {{-- OPTIONAL: TIMESTAMP --}}
                <div class="text-xs text-gray-400 mt-3">
                    Sent: {{ $f->created_at->diffForHumans() }}
                </div>

            </div>

        @empty
            <div class="text-center text-gray-500">
                No follow-ups yet.
            </div>
        @endforelse

    </div>
</x-app-layout>