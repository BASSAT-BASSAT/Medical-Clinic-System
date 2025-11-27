<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Notification;
use App\Mail\AppointmentReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Send appointment reminder emails
     * This can be called by a scheduled task
     */
    public static function sendAppointmentReminders()
    {
        $tomorrow = Carbon::tomorrow()->startOfDay();
        $tomorrowEnd = Carbon::tomorrow()->endOfDay();

        // Get all appointments tomorrow that haven't been reminded yet
        $appointments = Appointment::whereBetween('start_time', [$tomorrow, $tomorrowEnd])
            ->where('status', 'scheduled')
            ->with('patient', 'doctor')
            ->get();

        $sentCount = 0;

        foreach ($appointments as $appointment) {
            if ($appointment->patient && $appointment->patient->email) {
                try {
                    Mail::to($appointment->patient->email)->send(new AppointmentReminder($appointment));
                    
                    // Create notification record
                    Notification::create([
                        'appointment_id' => $appointment->appointment_id,
                        'patient_id' => $appointment->patient_id,
                        'doctor_id' => $appointment->doctor_id,
                        'type' => 'email',
                        'notification_type' => 'appointment_reminder',
                        'message' => 'Appointment reminder sent',
                        'recipient' => $appointment->patient->email,
                        'is_sent' => true,
                        'sent_at' => now(),
                    ]);

                    $sentCount++;
                } catch (\Exception $e) {
                    \Log::error('Failed to send reminder for appointment ' . $appointment->appointment_id . ': ' . $e->getMessage());
                }
            }
        }

        return $sentCount;
    }

    /**
     * Get unsent notifications
     */
    public static function getUnsentNotifications()
    {
        return Notification::where('is_sent', false)
            ->with('appointment', 'patient', 'doctor')
            ->orderBy('created_at', 'asc')
            ->limit(100)
            ->get();
    }

    /**
     * Send unsent notifications
     */
    public static function sendUnsentNotifications()
    {
        $notifications = static::getUnsentNotifications();
        $sentCount = 0;

        foreach ($notifications as $notification) {
            try {
                if ($notification->type === 'email' && $notification->recipient) {
                    $mailable = match ($notification->notification_type) {
                        'booking_confirmation' => new \App\Mail\AppointmentConfirmation($notification->appointment),
                        'appointment_reminder' => new AppointmentReminder($notification->appointment),
                        'appointment_cancelled' => new \App\Mail\AppointmentCancellation($notification->appointment),
                        default => null,
                    };

                    if ($mailable) {
                        Mail::to($notification->recipient)->send($mailable);
                        $notification->markAsSent();
                        $sentCount++;
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send notification ' . $notification->notification_id . ': ' . $e->getMessage());
            }
        }

        return $sentCount;
    }

    /**
     * Create notification for appointment
     */
    public static function createNotificationForAppointment(Appointment $appointment, $type, $notificationType)
    {
        return Notification::create([
            'appointment_id' => $appointment->appointment_id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'type' => $type,
            'notification_type' => $notificationType,
            'message' => ucfirst(str_replace('_', ' ', $notificationType)),
            'recipient' => $appointment->patient->email ?? null,
            'is_sent' => false,
        ]);
    }
}
