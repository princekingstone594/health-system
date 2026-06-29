<x-app-layout>
    <x-slot name="header">
        Edit Patient
    </x-slot>

```
<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    <form method="POST" action="{{ route('patients.update', $patient->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label>Name</label>
            <input type="text" name="name" value="{{ $patient->name }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label>Age</label>
            <input type="number" name="age" value="{{ $patient->age }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label>Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option {{ $patient->status == 'Stable' ? 'selected' : '' }}>Stable</option>
                <option {{ $patient->status == 'Under Observation' ? 'selected' : '' }}>Under Observation</option>
                <option {{ $patient->status == 'Critical' ? 'selected' : '' }}>Critical</option>
            </select>
        </div>

        <button class="bg-green-600 text-white px-4 py-2 rounded">
            Update Patient
        </button>
    </form>

</div>
```

</x-app-layout>
