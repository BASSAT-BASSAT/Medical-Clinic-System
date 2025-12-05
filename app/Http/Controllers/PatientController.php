<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Patient;
use App\Models\User;
use App\Mail\NewAccountSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    /**
     * Display patient dashboard
     */
    public function dashboard()
    {
        $patientId = auth()->user()->patient->patient_id ?? null;
        
        if (!$patientId) {
            return view('patient.dashboard');
        }
        
        $patient = Patient::with('appointments', 'medicalRecords')
            ->find($patientId);
        
        return view('patient.dashboard', compact('patient'));
    }

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
            'email' => 'nullable|email|unique:users,email',
        ]);

        $generatedPassword = null;
        $emailSent = false;
        $user = null;

        // If email provided, create a user so the patient can log in
        if (!empty($validated['email'])) {
            $generatedPassword = Str::random(10);
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => $generatedPassword, // User model has 'hashed' cast, auto-hashes
                'role' => 'patient',
            ]);

            $validated['user_id'] = $user->id;

            // Send welcome email with credentials
            try {
                Mail::to($user->email)->send(new NewAccountSetup($user, $generatedPassword, 'patient'));
                $emailSent = true;
            } catch (\Exception $e) {
                Log::error('Failed to send new account email to patient: ' . $e->getMessage());
                $emailSent = false;
            }
        }

        $patient = Patient::create($validated);

        // Create audit notification
        Notification::create([
            'patient_id' => $patient->patient_id,
            'type' => 'account_created',
            'notification_type' => 'system',
            'message' => "New patient account created: {$validated['first_name']} {$validated['last_name']}" . ($validated['email'] ? " ({$validated['email']})" : ''),
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        // Log the action
        Log::info('New patient account created', [
            'patient_id' => $patient->patient_id,
            'user_id' => $user?->id,
            'email' => $validated['email'] ?? 'none',
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        $response = $patient->toArray();
        if ($generatedPassword) {
            $response['generated_password'] = $generatedPassword;
            $response['email_sent'] = $emailSent;
        }

        return response()->json($response, 201);
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

    /**
     * Display appointment booking view
     */
    public function bookAppointment()
    {
        $patientId = auth()->user()->patient->patient_id ?? null;
        
        if (!$patientId) {
            return view('patient.book-appointment');
        }
        
        $patient = Patient::find($patientId);
        return view('patient.book-appointment', compact('patient'));
    }

    /**
     * Display patient's medical records view
     */
    public function medicalRecordsView()
    {
        $patientId = auth()->user()->patient->patient_id ?? null;
        
        if (!$patientId) {
            return view('patient.records');
        }
        
        $patient = Patient::with('medicalRecords')->find($patientId);
        return view('patient.records', compact('patient'));
    }

    /**
     * Display patient's notifications view
     */
    public function notifications()
    {
        $patientId = auth()->user()->patient->patient_id ?? null;
        
        if (!$patientId) {
            return view('patient.notifications');
        }
        
        $patient = Patient::find($patientId);
        return view('patient.notifications', compact('patient'));
    }
}

