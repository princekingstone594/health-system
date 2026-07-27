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
        <div class="flex gap-2">
             <input type="text" id="message" class="w-full border p-2 rounded"
                    placeholder="Type or speak your symptoms...">

             <button type="button" id="mic-btn"
                     class="bg-blue-600 text-white px-3 rounded">
                  🎙️
             </button>
        </div>
    </form>

</div>

<script>
// ===============================
// 💬 CHAT SUBMIT
// ===============================
document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();

    let messageInput = document.getElementById('message');
    let message = messageInput.value.trim();

    if (!message) return;

    let box = document.getElementById('chat-box');

    // 🧑 User message
    box.innerHTML += `
        <div class="mb-2">
            <strong>You:</strong>
            <p>${message}</p>
        </div>
    `;

    // 🤖 Typing indicator
    let typingId = 'typing-' + Date.now();

    box.innerHTML += `
        <div id="${typingId}" class="mb-2 text-gray-500 italic">
            AI is typing...
        </div>
    `;

    box.scrollTop = box.scrollHeight;

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

        // ❌ Remove typing
        let typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();

        // 🤖 AI reply
        box.innerHTML += `
            <div class="mb-2">
                <strong>AI:</strong>
                <p>${data.reply}</p>
            </div>
        `;

        // 🚨 Urgency UI
        if (data.urgency) {
            let color = 'green';
            let label = '🟢 Low';

            if (data.urgency.toLowerCase() === 'high') {
                color = 'red';
                label = '🔴 High - Seek immediate care';
            } else if (data.urgency.toLowerCase() === 'medium') {
                color = 'yellow';
                label = '🟡 Medium - Consider booking';
            }

            box.innerHTML += `
                <div class="mt-2 p-2 bg-${color}-100 text-${color}-700 rounded">
                    <strong>Urgency:</strong> ${label}
                </div>
            `;
        }

        // 👨‍⚕️ Doctors
        if (data.doctors && data.doctors.length > 0) {
            let doctorsHtml = `<div class="mt-3"><strong>👨‍⚕️ Recommended Doctors:</strong>`;

            data.doctors.forEach(doc => {
                doctorsHtml += `
                    <div class="border p-3 mt-2 rounded">
                        <p class="font-bold">${doc.name}</p>
                        <p>${doc.specialty}</p>
                        <p class="text-sm text-gray-600">${doc.location ?? ''}</p>

                        <a href="/appointments/create?doctor_id=${doc.id}"
                           class="bg-green-600 text-white px-3 py-1 rounded mt-1 inline-block">
                            Book Appointment
                        </a>
                    </div>
                `;
            });

            doctorsHtml += `</div>`;
            box.innerHTML += doctorsHtml;
        }

        // 🧹 Clear input AFTER send
        messageInput.value = '';
        box.scrollTop = box.scrollHeight;
    });
});
</script>

<script>
// ===============================
// 🎙️ VOICE INPUT (AUTO-SEND)
// ===============================
const micBtn = document.getElementById('mic-btn');
const messageInput = document.getElementById('message');

let recognition;

if ('webkitSpeechRecognition' in window) {
    recognition = new webkitSpeechRecognition();
    recognition.continuous = false;
    recognition.interimResults = false;
    recognition.lang = 'en-US';

    micBtn.addEventListener('click', () => {
        recognition.start();
        micBtn.innerText = '🎤 Listening...';
    });

    recognition.onresult = function(event) {
        let transcript = event.results[0][0].transcript;

        // ✍️ Put speech into input
        messageInput.value = transcript;

        // 🚀 Auto-send after short delay
        setTimeout(() => {
            document.getElementById('chat-form')
                .dispatchEvent(new Event('submit'));
        }, 400);

        micBtn.innerText = '🎙️';
    };

    recognition.onerror = function() {
        micBtn.innerText = '🎙️';
        alert('Voice recognition error. Try again.');
    };

    recognition.onend = function() {
        micBtn.innerText = '🎙️';
    };

} else {
    micBtn.disabled = true;
    micBtn.innerText = '❌';
}
</script>

</x-app-layout>