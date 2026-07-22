<x-app-layout>
<x-slot name="header">Book Appointment</x-slot>

<div class="bg-white p-6 rounded-xl shadow space-y-6">

    <!-- Doctor -->
    <div>
        <label class="block text-sm font-medium mb-1">Select Doctor</label>
        <select id="doctor" class="w-full border rounded-lg px-3 py-2">
            <option value="">-- Choose Doctor --</option>
            @foreach($doctors as $doc)
                <option value="{{ $doc->id }}">{{ $doc->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Date -->
    <div>
        <label class="block text-sm font-medium mb-1">Select Date</label>
        <input type="date" id="date" class="w-full border rounded-lg px-3 py-2">
    </div>

    <!-- Slots -->
    <div>
        <label class="block text-sm font-medium mb-2">Available Slots</label>
        <div id="slots" class="grid grid-cols-4 gap-2"></div>
    </div>

</div>

<script>
function loadSlots() {
    let doctor = document.getElementById('doctor').value;
    let date = document.getElementById('date').value;

    if (!doctor || !date) return;

    fetch(`/booking/slots?doctor_id=${doctor}&date=${date}`)
        .then(res => res.json())
        .then(data => {
            let container = document.getElementById('slots');
            container.innerHTML = '';

            if (data.length === 0) {
                container.innerHTML = '<p class="text-gray-500">No slots available</p>';
                return;
            }

            data.forEach(slot => {
                let btn = document.createElement('button');
                btn.innerText = slot;
                btn.className = "bg-blue-100 hover:bg-blue-500 hover:text-white px-3 py-2 rounded";

                btn.onclick = () => {
                    window.location.href = `/appointments/create?doctor_id=${doctor}&date=${date}&time=${slot}`;
                };

                container.appendChild(btn);
            });
        });
}

document.getElementById('doctor').addEventListener('change', loadSlots);
document.getElementById('date').addEventListener('change', loadSlots);
</script>

</x-app-layout>