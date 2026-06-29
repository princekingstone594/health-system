<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Patients
        Patient::create([
            'name' => 'John Doe',
            'age' => 45,
            'status' => 'Stable'
        ]);

        Patient::create([
            'name' => 'Jane Smith',
            'age' => 32,
            'status' => 'Under Observation'
        ]);

        Patient::create([
            'name' => 'Michael Lee',
            'age' => 60,
            'status' => 'Critical'
        ]);

        // Doctors
        Doctor::create(['name' => 'Dr. Adams']);
        Doctor::create(['name' => 'Dr. Sarah']);

        // Appointment
        Appointment::create([
            'patient_id' => 1,
            'date' => now()
        ]);
    }
}
