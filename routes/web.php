<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    | PROFILE
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | ADMIN + RECEPTIONIST (PATIENT MANAGEMENT)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,receptionist'])->group(function () {

        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');

        // ⚠️ KEEP LAST
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    });

    /*
    |--------------------------------------------------------------------------
    | APPOINTMENTS (ALL ROLES WITH DIFFERENT PERMISSIONS LATER)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,doctor,receptionist'])->group(function () {

        Route::get('/appointments/create/{patient?}', [AppointmentController::class, 'create'])
            ->name('appointments.create');

        Route::post('/appointments', [AppointmentController::class, 'store'])
            ->name('appointments.store');

        Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])
            ->name('appointments.edit');

        Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])
            ->name('appointments.update');

        Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
            ->name('appointments.cancel');

        Route::get('/appointments/booked-slots', [AppointmentController::class, 'getBookedSlots'])
            ->name('appointments.bookedSlots');
        
        Route::post('/appointments/ajax-store', [App\Http\Controllers\AppointmentController::class, 'ajaxStore'])
            ->name('appointments.ajax.store');
    });
});

Route::middleware(['auth', 'role:doctor'])->group(function () 
{
    Route::get('/availability', [DoctorAvailabilityController::class, 'index'])->name('availability.index');
    Route::get('/availability/create', [DoctorAvailabilityController::class, 'create'])->name('availability.create');
    Route::post('/availability', [DoctorAvailabilityController::class, 'store'])-name('availability.store');
    Route::get('/availability/calendar', [DoctorAvailabilityController::class, 'calendar'])->name('availability.calendar');
});

require __DIR__.'/auth.php';