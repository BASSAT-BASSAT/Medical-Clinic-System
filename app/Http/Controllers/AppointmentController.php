<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Notification;
use App\Events\AppointmentCreated;
use App\Events\AppointmentCancelled;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of all appointments
     */
    public function index()
    {
        $appointments = Appointment::with('doctor', 'patient')->paginate(15);
        return response()->json($appointments);
    }

    /**
     * Get available time slots for a doctor
     */
    public function availableSlots($doctorId, $date)
    {
        try {
            $doctor = Doctor::findOrFail($doctorId);
            
            // Check if doctor has any availability configured
            $doctorHasAvailability = $doctor->availability()->count() > 0;
            
            // Get existing appointments on this date (not cancelled)
            $appointments = Appointment::where('doctor_id', $doctorId)
                ->whereDate('start_time', $date)
                ->where('status', '!=', 'cancelled')
                ->get(['start_time', 'end_time']);

            // Get doctor availability for this day
            $dayOfWeek = Carbon::createFromFormat('Y-m-d', $date)->format('l');
            $availability = $doctor->availability()
                ->where('day_of_week', $dayOfWeek)
                ->where('is_available', true)
                ->first();

            if (!$availability) {
                $message = $doctorHasAvailability 
                    ? "Doctor is not available on $dayOfWeek"
                    : "This doctor has not set their availability yet. Please contact them or try another doctor.";
                return response()->json(['slots' => [], 'message' => $message]);
            }

            // Define slots based on doctor's availability
            $slots = [];
            $slotDuration = 60; // 1 hour in minutes
            
            // Parse start and end times (stored as HH:MM:SS strings)
            $startTimeStr = is_string($availability->start_time) ? $availability->start_time : $availability->start_time->format('H:i:s');
            $endTimeStr = is_string($availability->end_time) ? $availability->end_time : $availability->end_time->format('H:i:s');
            
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $startTimeStr);
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $endTimeStr);

            while ($startTime < $endTime) {
                $slotEnd = $startTime->clone()->addMinutes($slotDuration);
                
                // Check if slot is booked
                $isBooked = false;
                foreach ($appointments as $apt) {
                    $aptStart = Carbon::parse($apt->start_time);
                    $aptEnd = Carbon::parse($apt->end_time);
                    if (!($slotEnd <= $aptStart || $startTime >= $aptEnd)) {
                        $isBooked = true;
                        break;
                    }
                }
                
                if (!$isBooked) {
                    $slots[] = $startTime->format('H:i');
                }
                
                $startTime->addMinutes($slotDuration);
            }

            return response()->json(['slots' => $slots]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|exists:patients,patient_id',
                'doctor_id' => 'required|exists:doctors,doctor_id',
                'start_time' => 'required|date_format:"Y-m-d H:i:s"',
                'end_time' => 'required|date_format:"Y-m-d H:i:s"',
                'status' => 'sometimes|in:scheduled,completed,cancelled',
                'reason' => 'nullable|string|max:500',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Validation error: ' . $e->getMessage()], 422);
        }

        try {
            // Validate appointment duration doesn't exceed 4 hours
            $start = Carbon::createFromFormat('Y-m-d H:i:s', $validated['start_time']);
            $end = Carbon::createFromFormat('Y-m-d H:i:s', $validated['end_time']);
            
            if ($end->diffInHours($start) > 4) {
                return response()->json(['error' => 'Appointment duration cannot exceed 4 hours'], 422);
            }

            // Check for conflicting appointments
            $conflict = Appointment::where('doctor_id', $validated['doctor_id'])
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('start_time', '<', $validated['end_time'])
                              ->where('end_time', '>', $validated['start_time']);
                        });
                })
                ->first();

            if ($conflict) {
                return response()->json(['error' => 'Doctor has a conflicting appointment at this time'], 409);
            }

            // Check patient doesn't have conflicting appointments
            $patientConflict = Appointment::where('patient_id', $validated['patient_id'])
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('start_time', '<', $validated['end_time'])
                              ->where('end_time', '>', $validated['start_time']);
                        });
                })
                ->first();

            if ($patientConflict) {
                return response()->json(['error' => 'Patient already has an appointment at this time'], 409);
            }

            $validated['status'] = $validated['status'] ?? 'scheduled';
            \Log::info('About to create appointment', $validated);
            
            $appointment = Appointment::create($validated);
            \Log::info('Appointment created', ['id' => $appointment->appointment_id]);

            // For now, skip notifications to avoid errors
            // Just return the appointment
            return response()->json($appointment, 201);
        } catch (\Exception $e) {
            \Log::error('Appointment creation error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Error creating appointment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified appointment
     */
    public function show($id)
    {
        $appointment = Appointment::with('doctor', 'patient', 'medicalRecords', 'notifications')->findOrFail($id);
        return response()->json($appointment);
    }

    /**
     * Update the specified appointment
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'start_time' => 'sometimes|date_format:Y-m-d H:i:s|after:now',
            'end_time' => 'sometimes|date_format:Y-m-d H:i:s',
            'status' => 'sometimes|in:scheduled,completed,cancelled',
            'reason' => 'nullable|string|max:500',
        ]);

        // If updating time, check for conflicts
        if (isset($validated['start_time']) || isset($validated['end_time'])) {
            $startTime = $validated['start_time'] ?? $appointment->start_time;
            $endTime = $validated['end_time'] ?? $appointment->end_time;

            $conflict = Appointment::where('doctor_id', $appointment->doctor_id)
                ->where('appointment_id', '!=', $id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                })
                ->first();

            if ($conflict) {
                return response()->json(['error' => 'Doctor has a conflicting appointment at this time'], 409);
            }
        }

        // Handle cancellation
        $oldStatus = $appointment->status;
        $appointment->update($validated);

        if ($oldStatus !== 'cancelled' && ($validated['status'] ?? null) === 'cancelled') {
            $this->createNotification(
                $id,
                $appointment->patient_id,
                $appointment->doctor_id,
                'email',
                'appointment_cancelled',
                'Your appointment has been cancelled.',
                Patient::findOrFail($appointment->patient_id)->email
            );

            // Dispatch event to send cancellation email
            AppointmentCancelled::dispatch($appointment);
        }

        return response()->json($appointment);
    }

    /**
     * Delete the specified appointment
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json(['message' => 'Appointment deleted successfully']);
    }

    /**
     * Get appointments for a specific date
     */
    public function byDate($date)
    {
        $appointments = Appointment::whereDate('start_time', $date)
            ->with('doctor', 'patient')
            ->orderBy('start_time')
            ->get();
        return response()->json($appointments);
    }

    /**
     * Get appointments for a specific doctor
     */
    public function byDoctor($doctorId)
    {
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->with('patient')
            ->orderBy('start_time', 'desc')
            ->get();
        return response()->json($appointments);
    }

    /**
     * Get appointments for a specific patient
     */
    public function byPatient($patientId)
    {
        $appointments = Appointment::where('patient_id', $patientId)
            ->with('doctor')
            ->orderBy('start_time', 'desc')
            ->get();
        return response()->json($appointments);
    }

    /**
     * Get upcoming appointments for a patient
     */
    public function upcomingForPatient($patientId)
    {
        $appointments = Appointment::where('patient_id', $patientId)
            ->where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->with('doctor')
            ->orderBy('start_time')
            ->get();
        return response()->json($appointments);
    }

    /**
     * Get past appointments for a patient
     */
    public function pastForPatient($patientId)
    {
        $appointments = Appointment::where('patient_id', $patientId)
            ->where('start_time', '<', now())
            ->with('doctor')
            ->orderBy('start_time', 'desc')
            ->get();
        return response()->json($appointments);
    }

    /**
     * Helper method to create notifications
     */
    private function createNotification($appointmentId, $patientId, $doctorId, $type, $notificationType, $message, $recipient = null)
    {
        return Notification::create([
            'appointment_id' => $appointmentId,
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'type' => $type,
            'notification_type' => $notificationType,
            'message' => $message,
            'recipient' => $recipient,
            'is_sent' => false,
        ]);
    }
}
