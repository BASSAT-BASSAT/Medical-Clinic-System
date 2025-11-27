<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\DoctorAvailabilityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->group(function () {
    // Specialties
    Route::apiResource('specialties', SpecialtyController::class);

    // Doctors
    Route::apiResource('doctors', DoctorController::class);
    Route::get('doctors/{id}/appointments', [DoctorController::class, 'appointments']);
    Route::get('doctors/specialty/{specialtyId}', [DoctorController::class, 'bySpecialty']);
    Route::get('specialties/{specialtyId}/doctors', [DoctorController::class, 'bySpecialty']);

    // Doctor Availability
    Route::get('doctors/{doctorId}/availability', [DoctorAvailabilityController::class, 'index']);
    Route::post('availability', [DoctorAvailabilityController::class, 'store']);
    Route::put('availability/{id}', [DoctorAvailabilityController::class, 'update']);
    Route::delete('availability/{id}', [DoctorAvailabilityController::class, 'destroy']);
    Route::get('doctors/{doctorId}/availability/{dayOfWeek}', [DoctorAvailabilityController::class, 'byDay']);
    Route::post('availability/bulk-set', [DoctorAvailabilityController::class, 'bulkSet']);

    // Patients
    Route::apiResource('patients', PatientController::class);
    Route::get('patients/{id}/appointments', [PatientController::class, 'appointments']);
    Route::get('patients/{id}/medical-records', [PatientController::class, 'medicalRecords']);

    // Appointments
    Route::apiResource('appointments', AppointmentController::class);
    Route::get('appointments/by-date/{date}', [AppointmentController::class, 'byDate']);
    Route::get('appointments/by-doctor/{doctorId}', [AppointmentController::class, 'byDoctor']);
    Route::get('appointments/by-patient/{patientId}', [AppointmentController::class, 'byPatient']);
    Route::get('appointments/upcoming/patient/{patientId}', [AppointmentController::class, 'upcomingForPatient']);
    Route::get('appointments/past/patient/{patientId}', [AppointmentController::class, 'pastForPatient']);
    Route::get('doctors/{doctorId}/available-slots/{date}', [AppointmentController::class, 'availableSlots']);

    // Medical Records
    Route::apiResource('medical-records', MedicalRecordController::class);
    Route::get('medical-records/by-patient/{patientId}', [MedicalRecordController::class, 'byPatient']);
    Route::get('medical-records/by-appointment/{appointmentId}', [MedicalRecordController::class, 'byAppointment']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/patient/{patientId}', [NotificationController::class, 'patientNotifications']);
    Route::get('notifications/doctor/{doctorId}', [NotificationController::class, 'doctorNotifications']);
    Route::get('notifications/unsent', [NotificationController::class, 'unsent']);
    Route::get('notifications/appointment/{appointmentId}', [NotificationController::class, 'byAppointment']);
    Route::post('notifications', [NotificationController::class, 'store']);
    Route::put('notifications/{id}/mark-sent', [NotificationController::class, 'markAsSent']);
    Route::patch('notifications/{id}/mark-sent', [NotificationController::class, 'markAsSent']);
    Route::put('notifications/mark-all-sent', [NotificationController::class, 'markAllAsSent']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);

    // Reports
    Route::get('reports/daily/{date}', [ReportController::class, 'dailyReport']);
    Route::get('reports/weekly/{startDate}', [ReportController::class, 'weeklyReport']);
    Route::get('reports/monthly/{year}/{month}', [ReportController::class, 'monthlyReport']);
    Route::get('reports/doctor/{doctorId}/{startDate}/{endDate}', [ReportController::class, 'doctorReport']);
    Route::get('reports/patient/{patientId}', [ReportController::class, 'patientReport']);
    Route::get('reports/system-stats', [ReportController::class, 'systemStats']);
    Route::get('reports/export/daily/{date}', [ReportController::class, 'exportDaily']);
    Route::get('reports/export/weekly/{startDate}', [ReportController::class, 'exportWeekly']);
});
