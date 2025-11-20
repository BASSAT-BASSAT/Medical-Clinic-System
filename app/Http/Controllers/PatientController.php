<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of all patients
     */
    public function index()
    {
        $patients = Patient::paginate(15);
        return response()->json($patients);
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:patients,email',
        ]);

        $patient = Patient::create($validated);
        return response()->json($patient, 201);
    }

    /**
     * Display the specified patient
     */
    public function show($id)
    {
        $patient = Patient::with('appointments', 'medicalRecords')->findOrFail($id);
        return response()->json($patient);
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:patients,email,' . $id . ',patient_id',
        ]);

        $patient->update($validated);
        return response()->json($patient);
    }

    /**
     * Delete the specified patient
     */
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
        return response()->json(['message' => 'Patient deleted successfully']);
    }

    /**
     * Get patient's appointments
     */
    public function appointments($id)
    {
        $patient = Patient::findOrFail($id);
        $appointments = $patient->appointments()->with('doctor')->get();
        return response()->json($appointments);
    }

    /**
     * Get patient's medical records
     */
    public function medicalRecords($id)
    {
        $patient = Patient::findOrFail($id);
        $records = $patient->medicalRecords()->with('doctor', 'appointment')->get();
        return response()->json($records);
    }
}
