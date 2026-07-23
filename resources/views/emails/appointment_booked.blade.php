<h2>Appointment Confirmed</h2>

<p>Hello,</.>

<p>Your appointment has been booked successfully.</p>

<p><strong>Patient:</strong> {{ $appointment->patient->name }}</p>
<p><strong>Doctor:</strong> {{ $appointment->doctor->name }}</p>
<p><strong>Date:</strong> {{ $appointment->appointment_date }}</p>
<p><strong>Time:</strong> {{ $appointment->appointment_time }}</p>

<p>Thank you!</p>