<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Appointment;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications
     */
    public function index()
    {
        $notifications = Notification::with('appointment', 'patient', 'doctor')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return response()->json($notifications);
    }

    /**
     * Get notifications for a patient
     */
    public function patientNotifications($patientId)
    {
        $notifications = Notification::where('patient_id', $patientId)
            ->with('appointment', 'doctor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return response()->json($notifications);
    }

    /**
     * Get notifications for a doctor
     */
    public function doctorNotifications($doctorId)
    {
        $notifications = Notification::where('doctor_id', $doctorId)
            ->with('appointment', 'patient')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return response()->json($notifications);
    }

    /**
     * Get unsent notifications (for background jobs)
     */
    public function unsent()
    {
        $notifications = Notification::where('is_sent', false)
            ->with('appointment', 'patient', 'doctor')
            ->get();
        
        return response()->json($notifications);
    }

    /**
     * Mark a notification as sent
     */
    public function markAsSent($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsSent();
        
        return response()->json([
            'message' => 'Notification marked as sent',
            'notification' => $notification,
        ]);
    }

    /**
     * Create a notification (typically called by system events)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,appointment_id',
            'patient_id' => 'required|exists:patients,patient_id',
            'doctor_id' => 'required|exists:doctors,doctor_id',
            'type' => 'required|in:email,sms,both',
            'notification_type' => 'required|in:booking_confirmation,appointment_reminder,appointment_cancelled,appointment_completed',
            'message' => 'required|string',
            'recipient' => 'sometimes|string',
        ]);

        $notification = Notification::create($validated);
        return response()->json($notification, 201);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        
        return response()->json(['message' => 'Notification deleted successfully']);
    }

    /**
     * Get notifications for an appointment
     */
    public function byAppointment($appointmentId)
    {
        $notifications = Notification::where('appointment_id', $appointmentId)
            ->with('patient', 'doctor')
            ->get();
        
        return response()->json($notifications);
    }

    /**
     * Mark all unsent notifications as sent (for testing/admin)
     */
    public function markAllAsSent()
    {
        $count = Notification::where('is_sent', false)
            ->update([
                'is_sent' => true,
                'sent_at' => now(),
            ]);
        
        return response()->json([
            'message' => "Marked $count notifications as sent",
            'count' => $count,
        ]);
    }
}
