<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Get daily appointments report
     */
    public function dailyReport($date)
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
        
        $appointments = Appointment::whereDate('start_time', $carbonDate)
            ->with('doctor', 'patient')
            ->orderBy('start_time')
            ->get();

        $stats = [
            'date' => $date,
            'total_appointments' => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'scheduled' => $appointments->where('status', 'scheduled')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'stats' => $stats,
            'appointments' => $appointments,
        ]);
    }

    /**
     * Get weekly appointments report
     */
    public function weeklyReport($startDate)
    {
        $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $end = $start->clone()->endOfWeek();

        $appointments = Appointment::whereBetween('start_time', [$start, $end])
            ->with('doctor', 'patient')
            ->orderBy('start_time')
            ->get();

        // Group by day
        $appointmentsByDay = $appointments->groupBy(function ($item) {
            return $item->start_time->format('Y-m-d');
        });

        $stats = [
            'week_start' => $start->format('Y-m-d'),
            'week_end' => $end->format('Y-m-d'),
            'total_appointments' => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'scheduled' => $appointments->where('status', 'scheduled')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'daily_breakdown' => $appointmentsByDay->map(function ($dayAppointments) {
                return [
                    'total' => $dayAppointments->count(),
                    'completed' => $dayAppointments->where('status', 'completed')->count(),
                    'scheduled' => $dayAppointments->where('status', 'scheduled')->count(),
                    'cancelled' => $dayAppointments->where('status', 'cancelled')->count(),
                ];
            }),
        ];

        return response()->json([
            'stats' => $stats,
            'appointments' => $appointments,
        ]);
    }

    /**
     * Get monthly appointments report
     */
    public function monthlyReport($year, $month)
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end = $start->clone()->endOfMonth();

        $appointments = Appointment::whereBetween('start_time', [$start, $end])
            ->with('doctor', 'patient')
            ->orderBy('start_time')
            ->get();

        $stats = [
            'month' => $start->format('Y-m'),
            'total_appointments' => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'scheduled' => $appointments->where('status', 'scheduled')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'average_per_day' => $appointments->count() / $start->daysInMonth,
        ];

        return response()->json([
            'stats' => $stats,
            'appointments' => $appointments,
        ]);
    }

    /**
     * Get doctor-specific report for a date range
     */
    public function doctorReport($doctorId, $startDate, $endDate)
    {
        $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

        $doctor = Doctor::findOrFail($doctorId);

        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereBetween('start_time', [$start, $end])
            ->with('patient')
            ->orderBy('start_time')
            ->get();

        $stats = [
            'doctor_id' => $doctorId,
            'doctor_name' => $doctor->first_name . ' ' . $doctor->last_name,
            'specialty' => $doctor->specialty->name ?? 'N/A',
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'total_appointments' => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'scheduled' => $appointments->where('status', 'scheduled')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'completion_rate' => $appointments->count() > 0 
                ? round(($appointments->where('status', 'completed')->count() / $appointments->count()) * 100, 2)
                : 0,
        ];

        return response()->json([
            'stats' => $stats,
            'appointments' => $appointments,
        ]);
    }

    /**
     * Get patient-specific report
     */
    public function patientReport($patientId, $startDate = null, $endDate = null)
    {
        $patient = Patient::findOrFail($patientId);

        $query = Appointment::where('patient_id', $patientId)->with('doctor');

        if ($startDate && $endDate) {
            $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            $query->whereBetween('start_time', [$start, $end]);
        }

        $appointments = $query->orderBy('start_time', 'desc')->get();

        $stats = [
            'patient_id' => $patientId,
            'patient_name' => $patient->first_name . ' ' . $patient->last_name,
            'total_appointments' => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'scheduled' => $appointments->where('status', 'scheduled')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'stats' => $stats,
            'appointments' => $appointments,
        ]);
    }

    /**
     * Get overall system statistics
     */
    public function systemStats()
    {
        $totalAppointments = Appointment::count();
        $completedAppointments = Appointment::where('status', 'completed')->count();
        $scheduledAppointments = Appointment::where('status', 'scheduled')->count();
        $cancelledAppointments = Appointment::where('status', 'cancelled')->count();

        // Calculate average appointments per doctor
        $totalDoctors = Doctor::count();
        $avgAppointmentsPerDoctor = $totalDoctors > 0 ? $totalAppointments / $totalDoctors : 0;

        // Get today's appointments
        $todayAppointments = Appointment::whereDate('start_time', Carbon::today())->count();

        // Get upcoming appointments (next 7 days)
        $upcomingAppointments = Appointment::whereBetween('start_time', [
            Carbon::now(),
            Carbon::now()->addDays(7),
        ])->where('status', 'scheduled')->count();

        return response()->json([
            'overall_stats' => [
                'total_appointments' => $totalAppointments,
                'completed' => $completedAppointments,
                'scheduled' => $scheduledAppointments,
                'cancelled' => $cancelledAppointments,
                'completion_rate' => $totalAppointments > 0 
                    ? round(($completedAppointments / $totalAppointments) * 100, 2)
                    : 0,
                'cancellation_rate' => $totalAppointments > 0 
                    ? round(($cancelledAppointments / $totalAppointments) * 100, 2)
                    : 0,
            ],
            'doctor_stats' => [
                'total_doctors' => $totalDoctors,
                'avg_appointments_per_doctor' => round($avgAppointmentsPerDoctor, 2),
            ],
            'today_stats' => [
                'appointments_today' => $todayAppointments,
            ],
            'upcoming_stats' => [
                'appointments_next_7_days' => $upcomingAppointments,
            ],
        ]);
    }

    /**
     * Export daily report as array (can be converted to CSV/PDF)
     */
    public function exportDaily($date)
    {
        return $this->dailyReport($date);
    }

    /**
     * Export weekly report as array (can be converted to CSV/PDF)
     */
    public function exportWeekly($startDate)
    {
        return $this->weeklyReport($startDate);
    }
}
