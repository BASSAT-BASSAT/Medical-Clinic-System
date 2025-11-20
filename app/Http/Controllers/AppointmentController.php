<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;

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
        $doctor = Doctor::findOrFail($doctorId);
        
        // Get existing appointments on this date
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('start_time', $date)
            ->get(['start_time', 'end_time']);

        // Define business hours (9 AM to 5 PM, 1-hour slots)
        $slots = [];
        $startHour = 9;
        $endHour = 17;

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $slotTime = "$date $hour:00:00";
            $isBooked = $appointments->some(function ($apt) use ($slotTime) {
                return $slotTime >= $apt->start_time && $slotTime < $apt->end_time;
            });
            
            if (!$isBooked) {
                $slots[] = $slotTime;
            }
        }

        return response()->json($slots);
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'doctor_id' => 'required|exists:doctors,doctor_id',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'status' => 'sometimes|in:scheduled,completed,cancelled',
            'reason' => 'nullable|string|max:255',
        ]);

        // Check for conflicting appointments
        $conflict = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
            })
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Doctor has a conflicting appointment'], 409);
        }

        $appointment = Appointment::create($validated);
        return response()->json($appointment, 201);
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
            'start_time' => 'sometimes|date_format:Y-m-d H:i:s',
            'end_time' => 'sometimes|date_format:Y-m-d H:i:s',
            'status' => 'sometimes|in:scheduled,completed,cancelled',
            'reason' => 'nullable|string|max:255',
        ]);

        $appointment->update($validated);
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
}
