<x-app-layout>
    <x-slot name="header">
        Add Patient
    </x-slot>

```
<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('patients.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm mb-1">Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm mb-1">Age</label>
            <input type="number" name="age" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="Stable">Stable</option>
                <option value="Under Observation">Under Observation</option>
                <option value="Critical">Critical</option>
            </select>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Save Patient
        </button>

    </form>
</div>
```

</x-app-layout>
