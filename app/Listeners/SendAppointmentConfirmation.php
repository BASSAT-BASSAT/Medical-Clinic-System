<?php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Mail\AppointmentConfirmation;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendAppointmentConfirmation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(AppointmentCreated $event): void
    {
        $appointment = $event->appointment;
        $patient = $appointment->patient;
        
        if ($patient && $patient->email) {
            try {
                Mail::to($patient->email)->send(new AppointmentConfirmation($appointment));
                
                // Mark notification as sent
                Notification::where('appointment_id', $appointment->appointment_id)
                    ->where('notification_type', 'booking_confirmation')
                    ->update([
                        'is_sent' => true,
                        'sent_at' => now(),
                    ]);
            } catch (\Exception $e) {
                // Log error but don't fail
                \Log::error('Failed to send appointment confirmation: ' . $e->getMessage());
            }
        }
    }
}
