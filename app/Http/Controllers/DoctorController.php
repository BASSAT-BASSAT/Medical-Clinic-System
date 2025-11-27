<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display doctor dashboard
     */
    public function dashboard()
    {
        $doctorId = auth()->user()->doctor->doctor_id ?? null;
        
        if (!$doctorId) {
            return view('doctor.dashboard');
        }
        
        $doctor = Doctor::with('availability', 'appointments')
            ->find($doctorId);
        
        return view('doctor.dashboard', compact('doctor'));
    }

    /**
     * Display a listing of all doctors
     */
    public function index()
    {
        $doctors = Doctor::with('specialty')->paginate(15);
        return response()->json($doctors);
    }

    /**
     * Display doctors by specialty
     */
    public function bySpecialty($specialtyId)
    {
        $doctors = Doctor::where('specialty_id', $specialtyId)
            ->with('specialty')
            ->get();
        return response()->json($doctors);
    }

    /**
     * Store a newly created doctor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'specialty_id' => 'required|exists:specialties,specialty_id',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:doctors,email',
        ]);

        $doctor = Doctor::create($validated);
        return response()->json($doctor, 201);
    }

    /**
     * Display the specified doctor
     */
    public function show($id)
    {
        $doctor = Doctor::with('specialty', 'appointments')->findOrFail($id);
        return response()->json($doctor);
    }

    /**
     * Update the specified doctor
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'specialty_id' => 'sometimes|exists:specialties,specialty_id',
            'phone' => 'nullable|string|max:20',
            'email' => 'sometimes|email|unique:doctors,email,' . $id . ',doctor_id',
        ]);

        $doctor->update($validated);
        return response()->json($doctor);
    }

    /**
     * Delete the specified doctor
     */
    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();
        return response()->json(['message' => 'Doctor deleted successfully']);
    }

    /**
     * Get doctor's appointments
     */
    public function appointments($id)
    {
        $doctor = Doctor::findOrFail($id);
        $appointments = $doctor->appointments()->with('patient')->get();
        return response()->json($appointments);
    }

    /**
     * Display doctor reports
     */
    public function reports()
    {
        $doctorId = auth()->user()->doctor->doctor_id ?? null;
        
        if (!$doctorId) {
            return view('doctor.reports');
        }
        
        $doctor = Doctor::find($doctorId);
        $appointments = $doctor->appointments()
            ->with('patient')
            ->orderBy('start_time', 'desc')
            ->paginate(15);
        
        return view('doctor.reports', compact('doctor', 'appointments'));
    }

    /**
     * Display doctor's patients
     */
    public function patients()
    {
        $doctorId = auth()->user()->doctor->doctor_id ?? null;
        
        if (!$doctorId) {
            return view('doctor.patients');
        }
        
        $doctor = Doctor::find($doctorId);
        $patients = $doctor->appointments()
            ->distinct('patient_id')
            ->with('patient')
            ->get()
            ->pluck('patient');
        
        return view('doctor.patients', compact('doctor', 'patients'));
    }
}
