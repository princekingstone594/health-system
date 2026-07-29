<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <h2 class="text-xl font-bold mb-4">💬 Follow-Ups</h2>

        @foreach($followUps as $f)
            <div class="border p-4 mb-4 rounded bg-gray-50">

                <p class="mb-2">
                    <strong>🤖 AI:</strong> {{ $f->message }}
                </p>

                @if($f->response)
                    <p class="text-green-600">
                        <strong>🧑 You:</strong> {{ $f->response }}
                    </p>
                @else
                    <form method="POST" action="{{ route('followups.reply', $f->id) }}">
                        @csrf

                        <textarea name="response" class="w-full border rounded p-2 mb-2"
                                  placeholder="Type your update..."></textarea>

                        <button class="bg-blue-600 text-white px-3 py-1 rounded">
                            Send Response
                        </button>
                    </form>
                @endif

            </div>
        @endforeach

    </div>
</x-app-layout>