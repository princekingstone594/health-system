<h2>✅ Payment Successful</h2>

@if(isset($appointment))
    <p>Appointment ID: {{ $appointment->id }}</p>
    <p>Status: {{ $appointment->payment_status }}</p>
@endif

<a href="/">Go back</a>