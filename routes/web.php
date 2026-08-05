<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\DoctorAvailabilityController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StripePortalController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\ReceptionistDashboardController;
use App\Http\Controllers\SymptomCheckerController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\DoctorSummaryController;
use App\Http\Controllers\MedicalFileController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PatientHistoryController;
use App\Http\Controllers\FollowUpController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| GENERIC DASHBOARD (fallback only)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| AUTH REQUIRED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | ROLE-BASED DASHBOARDS (CORE FIX)
    |--------------------------------------------------------------------------
    */

    // ✅ Patient
    Route::middleware('role:patient')->group(function () {
        Route::get('/patient/dashboard', [PatientDashboardController::class, 'index'])
            ->name('patient.dashboard');

        Route::post('/patient/followup/trigger', [PatientDashboardController::class, 'triggerFollowUp'])
            ->name('patient.followup.trigger');
    });

    // ✅ Doctor
    Route::middleware('role:doctor')->group(function () {
        Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'index'])
            ->name('doctor.dashboard');

        Route::post('/doctor/appointment/{id}/status', [DoctorController::class, 'updateStatus'])
            ->name('doctor.appointment.status');

        Route::get('/doctor/appointment/{id}/notes', [DoctorController::class, 'addNotes'])
            ->name('doctor.notes');

        Route::post('/doctor/appointment/{id}/notes', [DoctorController::class, 'storeNotes'])
            ->name('doctor.notes.store');

        Route::get('/doctor/patient-records/{patient}', [MedicalRecordController::class, 'doctorView'])
            ->name('doctor.records');
    });

    // ✅ Receptionist
    Route::middleware('role:receptionist')->group(function () {
        Route::get('/receptionist/dashboard', [ReceptionistDashboardController::class, 'index'])
            ->name('receptionist.dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | APPOINTMENTS
    |--------------------------------------------------------------------------
    */
    Route::resource('appointments', AppointmentController::class);

    Route::post('/appointments/{appointment}/reschedule',
        [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');

    /*
    |--------------------------------------------------------------------------
    | BOOKING
    |--------------------------------------------------------------------------
    */
    Route::get('/book/{doctor}', [BookingController::class, 'show'])->name('booking.show');
    Route::post('/book', [BookingController::class, 'store'])->name('booking.store');

    /*
    |--------------------------------------------------------------------------
    | PAYMENTS
    |--------------------------------------------------------------------------
    */
    Route::post('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

    Route::post('/stripe/webhook', [StripeController::class, 'webhook']);

    /*
    |--------------------------------------------------------------------------
    | FOLLOW UPS (AI)
    |--------------------------------------------------------------------------
    */
    Route::get('/follow-ups', [FollowUpController::class, 'index'])->name('followups.index');
    Route::post('/followups/{id}', [FollowUpController::class, 'reply'])->name('followups.reply');

    Route::post('/followup/generate/{appointment}', [FollowUpController::class, 'generate'])
        ->name('followup.generate');

    /*
    |--------------------------------------------------------------------------
    | MEDICAL RECORDS
    |--------------------------------------------------------------------------
    */
    Route::get('/records', [MedicalRecordController::class, 'index'])->name('records.index');
    Route::post('/records/store', [MedicalRecordController::class, 'store'])->name('records.store');
    Route::get('/records/download/{id}', [MedicalRecordController::class, 'download'])->name('records.download');

    /*
    |--------------------------------------------------------------------------
    | AI + TOOLS
    |--------------------------------------------------------------------------
    */
    Route::get('/symptom-checker', [SymptomCheckerController::class, 'index'])->name('symptom.index');
    Route::post('/symptom-checker', [SymptomCheckerController::class, 'analyze'])->name('symptom.analyze');

    Route::get('/ai-chat', [AiChatController::class, 'index'])->name('ai.chat');
    Route::post('/ai-chat/send', [AiChatController::class, 'send']);

    /*
    |--------------------------------------------------------------------------
    | HISTORY
    |--------------------------------------------------------------------------
    */
    Route::get('/patient/history', [PatientHistoryController::class, 'index'])->name('patient.history');

    /*
    |--------------------------------------------------------------------------
    | POLICIES
    |--------------------------------------------------------------------------
    */
    Route::view('/privacy-policy', 'policies.privacy')->name('privacy');
    Route::view('/terms', 'policies.terms')->name('terms');
});

require __DIR__.'/auth.php';