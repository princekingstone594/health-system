<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Appointment;

class AppointmentUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;
    protected $type; // cancel or reschedule

    public function __construct(Appointment $appointment, $type)
    {
        $this->appointment = $appointment;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $doctor = $this->appointment->doctor->name ?? 'Doctor';
        $date = $this->appointment->appointment_date;
        $time = $this->appointment->appointment_time;

        if ($this->type === 'cancel') {
            return (new MailMessage)
                ->subject('Appointment Cancelled')
                ->greeting('Hello!')
                ->line("Your appointment with Dr. {$doctor} has been cancelled.")
                ->line("Date: {$date}")
                ->line("Time: {$time}")
                ->line('If this was a mistake, please rebook.')
                ->line('Thank you.');
        }

        if ($this->type === 'reschedule') {
            return (new MailMessage)
                ->subject('Appointment Rescheduled')
                ->greeting('Hello!')
                ->line("Your appointment with Dr. {$doctor} has been rescheduled.")
                ->line("New Date: {$date}")
                ->line("New Time: {$time}")
                ->line('Please make sure to attend.')
                ->line('Thank you.');
        }
    }
}