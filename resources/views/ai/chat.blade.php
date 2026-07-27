<x-app-layout>
<div class="max-w-2xl mx-auto p-4">

    <h2 class="text-xl font-bold mb-4">💬 AI Medical Assistant</h2>

    <div id="chat-box" class="border p-4 h-96 overflow-y-auto mb-3 bg-gray-50">

        @foreach($messages as $msg)
            <div class="mb-2">
                <strong>{{ $msg->role === 'user' ? 'You' : 'AI' }}:</strong>
                <p>{{ $msg->message }}</p>
            </div>
        @endforeach

    </div>

    <form id="chat-form">
        @csrf
        <input type="text" id="message" class="w-full border p-2 rounded" placeholder="Type your symptoms...">
    </form>

</div>

<script>
document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();

    let message = document.getElementById('message').value;

    fetch('/ai-chat/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message })
    })
    .then(res => res.json())
    .then(data => {
        let box = document.getElementById('chat-box');

        box.innerHTML += `<div><strong>You:</strong><p>${message}</p></div>`;
        box.innerHTML += `<div><strong>AI:</strong><p>${data.reply}</p></div>`;

        document.getElementById('message').value = '';
        box.scrollTop = box.scrollHeight;
    });
});
</script>

</div>
</x-app-layout>