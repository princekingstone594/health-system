<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\DoctorAvailabilityController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controller\StripeController;
use App\Http\Controller\AdminController;
use App\Http\Controllers\StripePortalController;
use App\Http\Controller\DoctorController;

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

    Route::get('/booking', [AppointmentController::class, 'booking'])
         ->name('appointments.booking');

    Route::get('/bookings/slots', [AppointmentController::class, 'getAvailableSlots'])
         ->name('appointment.slots');

    Route::get('/book/{doctor}', [BookingController::class, 'show'])->name('booking.show');
    Route::post('/book', [BookingController::class, 'store'])->name('booking.store');

    // API for time slots
    Route::get('/booking/slots', [BookingController::class, 'slots'])->name('booking.slots');

    Route::get('/appointments/book', [AppointmentController::class, 'booking'])
         ->name('appointments.booking');

    Route::get('/appointment/slots', [AppointmentController::class, 'slots']);

    Route::put('/appointments/{appointmet}/reschedule',
       [App\Http\Controllers\AppoitnmentController::class, 'reschedule'])->name('appointments.reschedule');

    Route::post('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::post('/checkout/{id}', [StripeController::class, 'checkout'])->name('stripe.checkout');

    Route::get('/payment/success', [StripeController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [StripeController::class, 'cancel'])->name('payment.cancel');
       
    Route::post('/stripe/webhook', [StripeController::class, 'webhook']);
     
    Route::get('/admin/revenue', [AdminController::class, 'revenue'])->name('admin.revenue');

    Route::get('/admin/dashboard', [AdminCpntroller::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/plans', [SubscriptionController::class, 'plans'])->name('plans');

    Route::get('/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscribe');

    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');

    Route::get('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subsription.cancel');

    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    Route::resource('appointments', AppointmentController::class);

    Route::get('/billing/portal', [StripePortalController::class, 'portal'])->name('billing.portal');

    Route::get('/doctor/availability', [DoctorAvailabilityController::class, 'index']);
    Route::post('/doctor/availability', [DoctorAvailabilityController::class, 'store']);

    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');

    Route::post('/doctor/approve/{id}', [DoctorController::class, 'approve'])->name('doctor.approve');

    Route::post('/doctor/reject/{id}', [DoctorController::class, 'reject'])->name('doctor.reject');

    Route::post('/doctor/appointment/{id}/status', [DoctorController::class, 'updateStatus'])->name('doctor.appointment.status');

  
    /*
    |--------------------------------------------------------------------------
    | PATIENT MANAGEMENT
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,receptionist'])->group(function () {

        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    });

    /*
    |--------------------------------------------------------------------------
    | APPOINTMENTS
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

        // ✅ AJAX create (calendar)
        Route::post('/appointments/ajax-store', [AppointmentController::class, 'ajaxStore'])
            ->name('appointments.ajax.store');

        Route::post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

        Route::get('/appointments/{id}/reschedule', [AppointmentController::class, 'rescheduleForm'])->name('appointments.reschedule.form');

        Route::post('/appointments/{id}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    });

    /*
    |--------------------------------------------------------------------------
    | DOCTOR AVAILABILITY + CALENDAR
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:doctor'])->group(function () {

        Route::get('/availability', [DoctorAvailabilityController::class, 'index'])->name('availability.index');
        Route::get('/availability/create', [DoctorAvailabilityController::class, 'create'])->name('availability.create');
        Route::post('/availability', [DoctorAvailabilityController::class, 'store'])->name('availability.store');
        Route::get('/availability/calendar', [DoctorAvailabilityController::class, 'calendar'])->name('availability.calendar');

        // ✅ LEAVE SYSTEM
        Route::resource('leaves', LeaveController::class);

        Route::get('/test-sms', [DashboardController::class, 'testSms']);

        Route::resource('schedules', ScheduleController::class);
    });

    Route::middleware(['auth', 'doctor'])->group(function () {
        Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');

        Route::post('/doctor/appointments/{id}/status', [DoctorController::class, 'updateStatus'])->name('doctor.appointment.status');

        Route::get('/doctor/calendar', [DoctorController::class, 'calendar'])->name('doctor.calendar');
    });
    
    Route::middleware(['auth', 'patient'])->group(function () {
        Route::get('/patient/dashboard', [PatientDashboardController::class, 'index'])->name('patient.dashboard');
    });
});

require __DIR__.'/auth.php';