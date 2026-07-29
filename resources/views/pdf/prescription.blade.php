<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prescription</title>
    <style>
        body {
            font-family: DejaVu Sans;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 15px;
        }
        .box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>🩺 Medical Prescription</h2>
        <p>Generated on {{ now()->format('d M Y') }}</p>
    </div>

    <div class="section">
        <p><span class="label">Doctor:</span> Dr. {{ $appointment->doctor->name }}</p>
        <p><span class="label">Patient:</span> {{ $appointment->patient->name }}</p>
        <p><span class="label">Date:</span> {{ $appointment->appointment_date }}</p>
    </div>

    <div class="section box">
        <p class="label">Symptoms:</p>
        <p>{{ $appointment->symptoms ?? 'N/A' }}</p>
    </div>

    <div class="section box">
        <p class="label">Diagnosis:</p>
        <p>{{ $appointment->diagnosis ?? 'N/A' }}</p>
    </div>

    <div class="section box">
        <p class="label">Prescription:</p>
        <p>{{ $appointment->prescription ?? 'N/A' }}</p>
    </div>

    <div class="section box">
        <p class="label">Doctor Notes:</p>
        <p>{{ $appointment->doctor_notes ?? 'N/A' }}</p>
    </div>

    <div style="margin-top: 40px;">
        <p>__________________________</p>
        <p>Doctor Signature</p>
    </div>

</body>
</html>