<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Specialty;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $totalDoctors = Doctor::count();
        $totalPatients = Patient::count();
        $totalAppointments = Appointment::count();
        $totalSpecialties = Specialty::count();
        $totalRecords = MedicalRecord::count();
        
        $upcomingAppointments = Appointment::where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('start_time')
            ->limit(10)
            ->with('doctor', 'patient')
            ->get();
        
        return view('admin.dashboard', compact(
            'totalDoctors',
            'totalPatients',
            'totalAppointments',
            'totalSpecialties',
            'totalRecords',
            'upcomingAppointments'
        ));
    }

    /**
     * Get system statistics
     */
    public function statistics()
    {
        return response()->json([
            'total_doctors' => Doctor::count(),
            'total_patients' => Patient::count(),
            'total_appointments' => Appointment::count(),
            'total_specialties' => Specialty::count(),
            'total_records' => MedicalRecord::count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
            'cancelled_appointments' => Appointment::where('status', 'cancelled')->count(),
            'scheduled_appointments' => Appointment::where('status', 'scheduled')->count(),
        ]);
    }
}
