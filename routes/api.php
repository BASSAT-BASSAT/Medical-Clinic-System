<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;

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
    Route::get('specialties/{specialtyId}/doctors', [DoctorController::class, 'bySpecialty']);

    // Patients
    Route::apiResource('patients', PatientController::class);
    Route::get('patients/{id}/appointments', [PatientController::class, 'appointments']);
    Route::get('patients/{id}/medical-records', [PatientController::class, 'medicalRecords']);

    // Appointments
    Route::apiResource('appointments', AppointmentController::class);
    Route::get('appointments/by-date/{date}', [AppointmentController::class, 'byDate']);
    Route::get('appointments/by-doctor/{doctorId}', [AppointmentController::class, 'byDoctor']);
    Route::get('appointments/by-patient/{patientId}', [AppointmentController::class, 'byPatient']);
    Route::get('doctors/{doctorId}/available-slots/{date}', [AppointmentController::class, 'availableSlots']);

    // Medical Records
    Route::apiResource('medical-records', MedicalRecordController::class);
    Route::get('medical-records/by-patient/{patientId}', [MedicalRecordController::class, 'byPatient']);
    Route::get('medical-records/by-appointment/{appointmentId}', [MedicalRecordController::class, 'byAppointment']);
});
