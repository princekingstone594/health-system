<x-app-layout>
<div class="max-w-2xl mx-auto p-4">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">💭 AI Medical Assistant</h2>

        <button id="voice-toggle"
                class="bg-gray-200 px-3 py-1 rounded text-sm">
            🔊 Voice ON
        </button>
    </div>

    <!-- DOCTOR PERSONALITY -->
    <div class="mb-3">
        <label class="block font-semibold mb-1">🧑‍⚕️ Doctor Style</label>
        <select id="personality" class="border p-2 rounded w-full">
            <option value="friendly">😊 Friendly Doctor</option>
            <option value="professional">🧑‍⚕️ Professional</option>
            <option value="calm">🙎 Calm & Reassuring</option>
            <option value="emergency">🚨 Emergency Mode</option>
        </select>
    </div>

    <!-- CHAT BOX -->
    <div id="chat-box" class="border p-4 h-96 overflow-y-auto mb-3 bg-gray-50">
        @foreach($messages as $msg)
            <div class="mb-2">
                <strong>{{ $msg->role === 'user' ? 'You' : 'AI' }}:</strong>
                <p>{{ $msg->message }}</p>
            </div>
        @endforeach
    </div>

    <!-- INPUT -->
    <form id="chat-form">
        @csrf
        <div class="flex gap-2">
            <input type="text" id="message"
                   class="w-full border p-2 rounded"
                   placeholder="Type or speak your symptoms...">

            <button type="button" id="mic-btn"
                    class="bg-blue-600 text-white px-3 rounded">
                🎙️
            </button>
        </div>
    </form>

    <!-- DOCTOR SUMMARY BUTTON -->
    <button onclick="getDoctorSummary()"
            class="bg-blue-600 text-white px-4 py-2 rounded mt-3">
        🧾 Generate Doctor Summary
    </button>

    <!-- SUMMARY BOX -->
    <div id="summaryBox"
         class="mt-4 p-4 border rounded bg-gray-100 whitespace-pre-line">
    </div>

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

    // USER MESSAGE
    box.innerHTML += `
        <div class="mb-2">
            <strong>You:</strong>
            <p>${escapeHtml(message)}</p>
        </div>
    `;

    // TYPING INDICATOR
    let typingId = 'typing-' + Date.now();

    box.innerHTML += `
        <div id="${typingId}" class="mb-2 text-gray-500 italic">
            AI is typing...
        </div>
    `;

    box.scrollTop = box.scrollHeight;

    let personality = document.getElementById('personality').value;

    fetch('/ai-chat/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message, personality })
    })
    .then(res => res.json())
    .then(data => {

        document.getElementById(typingId)?.remove();

        // AI RESPONSE
        box.innerHTML += `
            <div class="mb-2">
                <strong>AI:</strong>
                <p>${escapeHtml(data.reply)}</p>
            </div>
        `;

        // URGENCY DISPLAY
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

        // 🔊 SPEAK ALWAYS (not only urgency)
        speak(data.reply);

        // DOCTOR RECOMMENDATIONS
        if (data.doctors && data.doctors.length > 0) {
            let doctorsHtml = `<div class="mt-3"><strong>👨‍⚕️ Recommended Doctors:</strong>`;

            data.doctors.forEach(doc => {
                doctorsHtml += `
                    <div class="border p-3 mt-2 rounded">
                        <p class="font-bold">${escapeHtml(doc.name)}</p>
                        <p>${escapeHtml(doc.specialty)}</p>
                        <p class="text-sm text-gray-600">${escapeHtml(doc.location ?? '')}</p>

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

        messageInput.value = '';
        box.scrollTop = box.scrollHeight;
    });
});

// Escape HTML (security fix)
function escapeHtml(text) {
    const div = document.createElement('div');
    div.innerText = text;
    return div.innerHTML;
}
</script>

<script>
// ===============================
// 🧾 DOCTOR SUMMARY
// ===============================
async function getDoctorSummary() {
    const box = document.getElementById('summaryBox');
    box.innerText = 'Generating summary... ⏳';

    try {
        const res = await fetch('/doctor-summary', {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await res.json();
        box.innerText = data.summary;

    } catch (err) {
        box.innerText = 'Failed to generate summary ❌';
    }
}
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
        messageInput.value = transcript;

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

<script>
// ===============================
// 🔊 AI VOICE REPLY
// ===============================
let voiceEnabled = true;

const voiceToggle = document.getElementById('voice-toggle');

voiceToggle.addEventListener('click', () => {
    voiceEnabled = !voiceEnabled;

    voiceToggle.innerText = voiceEnabled
        ? '🔊 Voice ON'
        : '🔇 Voice OFF';
});

function speak(text) {
    if (!voiceEnabled) return;
    if (!('speechSynthesis' in window)) return;

    const speech = new SpeechSynthesisUtterance(text);

    speech.rate = 0.95;
    speech.pitch = 1;
    speech.volume = 1;

    const voices = speechSynthesis.getVoices();
    const preferredVoice = voices.find(v =>
        v.name.includes('Google') || v.name.includes('Female')
    );

    if (preferredVoice) {
        speech.voice = preferredVoice;
    }

    speechSynthesis.cancel();
    speechSynthesis.speak(speech);
}
</script>

</x-app-layout>