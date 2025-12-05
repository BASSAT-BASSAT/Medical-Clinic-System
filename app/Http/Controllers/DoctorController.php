<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Notification;
use App\Models\Specialty;
use App\Models\User;
use App\Mail\NewAccountSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            'email' => 'required|email|unique:users,email',
        ]);

        // Create corresponding User so the doctor can log in
        $password = Str::random(10);
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => $password, // User model has 'hashed' cast, auto-hashes
            'role' => 'doctor',
        ]);

        // Attach user_id to doctor record
        $doctorData = $validated;
        $doctorData['user_id'] = $user->id;

        $doctor = Doctor::create($doctorData);

        // Send welcome email with credentials
        try {
            Mail::to($user->email)->send(new NewAccountSetup($user, $password, 'doctor'));
            $emailSent = true;
        } catch (\Exception $e) {
            Log::error('Failed to send new account email to doctor: ' . $e->getMessage());
            $emailSent = false;
        }

        // Create audit notification
        Notification::create([
            'doctor_id' => $doctor->doctor_id,
            'type' => 'account_created',
            'notification_type' => 'system',
            'message' => "New doctor account created: Dr. {$validated['first_name']} {$validated['last_name']} ({$validated['email']})",
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        // Log the action
        Log::info('New doctor account created', [
            'doctor_id' => $doctor->doctor_id,
            'user_id' => $user->id,
            'email' => $user->email,
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        return response()->json(array_merge($doctor->toArray(), [
            'generated_password' => $password,
            'email_sent' => $emailSent,
        ]), 201);
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
