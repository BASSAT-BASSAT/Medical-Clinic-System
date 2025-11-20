<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Appointment;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of all medical records
     */
    public function index()
    {
        $records = MedicalRecord::with('patient', 'doctor', 'appointment')->paginate(15);
        return response()->json($records);
    }

    /**
     * Store a newly created medical record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'doctor_id' => 'required|exists:doctors,doctor_id',
            'appointment_id' => 'required|exists:appointments,appointment_id',
            'record_date' => 'nullable|date_format:Y-m-d H:i:s',
            'notes' => 'nullable|string',
        ]);

        // Use current timestamp if not provided
        if (!isset($validated['record_date'])) {
            $validated['record_date'] = now();
        }

        $record = MedicalRecord::create($validated);
        return response()->json($record, 201);
    }

    /**
     * Display the specified medical record
     */
    public function show($id)
    {
        $record = MedicalRecord::with('patient', 'doctor', 'appointment')->findOrFail($id);
        return response()->json($record);
    }

    /**
     * Update the specified medical record
     */
    public function update(Request $request, $id)
    {
        $record = MedicalRecord::findOrFail($id);

        $validated = $request->validate([
            'notes' => 'sometimes|string',
        ]);

        $record->update($validated);
        return response()->json($record);
    }

    /**
     * Delete the specified medical record
     */
    public function destroy($id)
    {
        $record = MedicalRecord::findOrFail($id);
        $record->delete();
        return response()->json(['message' => 'Medical record deleted successfully']);
    }

    /**
     * Get records for a specific patient
     */
    public function byPatient($patientId)
    {
        $records = MedicalRecord::where('patient_id', $patientId)
            ->with('doctor', 'appointment')
            ->orderBy('record_date', 'desc')
            ->get();
        return response()->json($records);
    }

    /**
     * Get records for a specific appointment
     */
    public function byAppointment($appointmentId)
    {
        $records = MedicalRecord::where('appointment_id', $appointmentId)
            ->with('patient', 'doctor')
            ->get();
        return response()->json($records);
    }
}
