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

    let messageInput = document.getElementById('message');
    let message = messageInput.value.trim();

    if (!message) return;

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

        // User message
        box.innerHTML += `
            <div class="mb-2">
                <strong>You:</strong>
                <p>${message}</p>
            </div>
        `;

        // AI reply
        box.innerHTML += `
            <div class="mb-2">
                <strong>AI:</strong>
                <p>${data.reply}</p>
            </div>
        `;

        if (data.urgency) {
            let color = 'green';
            let label = '🟢 Low';

            if (data.urgency.toLowerCase() === 'high') {
                color = 'red';
                label = '🔴 High - Seek immediate care';
            } else if (data.urgency.toLowercase() === 'medium') {
                color = 'yellow';
                label = '🟡 Medium - consider booking';
            }

            box.innerHTML += `
                 <div class="mt-2 p-2 bg-${color}-100 text-${color}-700 rounded">
                      <strong>Urgency:</strong> ${label}
                </div>
            `;
        }

        // 👨‍⚕️ Recommended doctors
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

        messageInput.value = '';
        box.scrollTop = box.scrollHeight;
    });
});
</script>

</x-app-layout>