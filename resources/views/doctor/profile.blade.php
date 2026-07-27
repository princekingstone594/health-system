<form method="POST" action="{{ route('doctor.profile.update') }}">
    @csrf

    <input type="text" name="specialty" placeholder="Specialty (e.g. Cardiologist)" class="w-full border p-2 mb-2">

    <textarea name="qualifications" placeholder="Qualifications" class="w-full border p-2 mb-2"></textarea>

    <input type="text" name="location" placeholder="Location" class="w-full border p-2 mb-2">

    <input type="number" name="experience_years" placeholder="Years of Experience" class="w-full border p-2 mb-2">

    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Save Profile
    </button>
</form>