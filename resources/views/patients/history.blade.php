<x-app-layout>
    <div class="max-w-6xl mx-auto p-6">

        <h2 class="text-2xl font-bold mb-6">📋 My Medical History</h2>

        {{-- ✅ Success Message --}}
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($appointments->isEmpty())
            <p class="text-gray-500">No medical history yet.</p>
        @else

            <div class="space-y-6">

                @foreach($appointments as $appointment)
                    <div class="border rounded p-4 bg-white shadow">

                        {{-- Header --}}
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="font-bold text-lg">
                                {{ $appointment->appointment_date }} at {{ $appointment->appointment_time }}
                            </h3>

                            <span class="text-sm px-2 py-1 rounded
                                @if($appointment->status === 'cancelled') bg-red-100 text-red-600
                                @elseif($appointment->status === 'completed') bg-green-100 text-green-600
                                @else bg-yellow-100 text-yellow-700
                                @endif
                            ">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>

                        {{-- Doctor --}}
                        <p class="text-sm text-gray-700 mb-2">
                            👨‍⚕️ Doctor: {{ $appointment->doctor->name ?? 'N/A' }}
                        </p>

                        {{-- Reason --}}
                        @if($appointment->reason)
                            <div class="mb-2">
                                <strong>📝 Reason:</strong>
                                <p class="text-sm">{{ $appointment->reason }}</p>
                            </div>
                        @endif

                        {{-- Symptoms --}}
                        @if($appointment->symptoms)
                            <div class="mb-2">
                                <strong>🩺 Symptoms:</strong>
                                <p class="text-sm">{{ $appointment->symptoms }}</p>
                            </div>
                        @endif

                        {{-- AI Summary --}}
                        @if($appointment->ai_summary)
                            <div class="mb-2 p-3 bg-blue-50 rounded">
                                <strong>🧠 AI Summary:</strong>
                                <p class="text-sm whitespace-pre-line">{{ $appointment->ai_summary }}</p>
                            </div>
                        @endif

                        {{-- Doctor Notes --}}
                        @if($appointment->doctor_notes)
                            <div class="mb-2 p-3 bg-green-50 rounded">
                                <strong>🧾 Doctor Notes:</strong>
                                <p class="text-sm">{{ $appointment->doctor_notes }}</p>
                            </div>
                        @endif

                        {{-- Diagnosis --}}
                        @if($appointment->diagnosis)
                            <div class="mb-2">
                                <strong>💊 Diagnosis:</strong>
                                <p class="text-sm">{{ $appointment->diagnosis }}</p>
                            </div>
                        @endif

                        {{-- Prescription --}}
                        @if($appointment->prescription)
                            <div class="mb-2">
                                <strong>💉 Prescription:</strong>
                                <p class="text-sm">{{ $appointment->prescription }}</p>
                            </div>
                        @endif

                        <a href="{{ route('appointments.prescription.download', $appointment->id) }}"
                           class="inline-block mt-3 bg-blue-600 text-white px-3 rounded text-sm">
                             📃 Download Prescription
                        </a>

                        {{-- ============================= --}}
                        {{-- 📎 FILE UPLOAD --}}
                        {{-- ============================= --}}
                        <div class="mt-4 border-t pt-4">

                            <form method="POST"
                                  action="{{ route('medical-files.store') }}"
                                  enctype="multipart/form-data"
                                  class="flex flex-col sm:flex-row gap-2 items-start sm:items-center">
                                @csrf

                                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

                                <input type="file"
                                       name="file"
                                       class="border p-2 rounded w-full sm:w-auto">

                                <button class="bg-blue-500 text-white px-3 py-1 rounded text-sm">
                                    Upload Report
                                </button>
                            </form>

                            {{-- Validation Errors --}}
                            @error('file')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror

                            {{-- ============================= --}}
                            {{-- 📂 FILE LIST --}}
                            {{-- ============================= --}}
                            @if($appointment->medicalFiles->count())
                                <div class="mt-4">
                                    <strong>📎 Uploaded Files:</strong>

                                    <div class="mt-2 space-y-2">
                                        @foreach($appointment->medicalFiles as $file)
                                            <div class="flex justify-between items-center text-sm border p-2 rounded bg-gray-50">

                                                <span class="truncate">
                                                    {{ $file->original_name }}
                                                </span>

                                                <div class="flex items-center gap-3">

                                                    {{-- Download --}}
                                                    <a href="{{ route('medical-files.download', $file->id) }}"
                                                       class="text-blue-600 hover:underline">
                                                        Download
                                                    </a>

                                                    {{-- Delete --}}
                                                    <form method="POST"
                                                          action="{{ route('medical-files.delete', $file->id) }}"
                                                          onsubmit="return confirm('Delete this file?')">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button class="text-red-500 hover:underline">
                                                            Delete
                                                        </button>
                                                    </form>

                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>
                @endforeach

            </div>

        @endif

    </div>
</x-app-layout>