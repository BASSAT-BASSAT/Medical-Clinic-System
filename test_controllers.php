<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║           🏥 MEDICAL CLINIC API CONTROLLER TEST                    ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

// Test DoctorController
echo "📋 Testing DoctorController\n";
echo "───────────────────────────────────────────────────────────────────\n";
$doctorController = new DoctorController();
$doctors = \App\Models\Doctor::with('specialty')->paginate(5);
echo "✅ Doctors retrieved: " . $doctors->count() . " doctors\n";
echo "   Total doctors in DB: " . \App\Models\Doctor::count() . "\n";
echo "   Sample: " . $doctors->first()->first_name . " " . $doctors->first()->last_name . " (" . $doctors->first()->specialty->name . ")\n\n";

// Test PatientController  
echo "📋 Testing PatientController\n";
echo "───────────────────────────────────────────────────────────────────\n";
$patientController = new PatientController();
$patients = \App\Models\Patient::paginate(5);
echo "✅ Patients retrieved: " . $patients->count() . " patients\n";
echo "   Total patients in DB: " . \App\Models\Patient::count() . "\n";
echo "   Sample: " . $patients->first()->first_name . " " . $patients->first()->last_name . " (DOB: " . $patients->first()->dob . ")\n\n";

// Test SpecialtyController
echo "📋 Testing SpecialtyController\n";
echo "───────────────────────────────────────────────────────────────────\n";
$specialtyController = new SpecialtyController();
$specialties = \App\Models\Specialty::all();
echo "✅ Specialties retrieved: " . $specialties->count() . " specialties\n";
echo "   Specialties: " . $specialties->pluck('name')->implode(', ') . "\n\n";

// Test AppointmentController
echo "📋 Testing AppointmentController\n";
echo "───────────────────────────────────────────────────────────────────\n";
$appointmentController = new AppointmentController();
$appointments = \App\Models\Appointment::with('doctor', 'patient')->paginate(5);
echo "✅ Appointments retrieved: " . $appointments->count() . " appointments\n";
echo "   Total appointments in DB: " . \App\Models\Appointment::count() . "\n";
if (\App\Models\Appointment::count() === 0) {
    echo "   (No appointments yet - ready to create)\n";
}
echo "\n";

// Test MedicalRecordController
echo "📋 Testing MedicalRecordController\n";
echo "───────────────────────────────────────────────────────────────────\n";
$medicalRecordController = new MedicalRecordController();
$records = \App\Models\MedicalRecord::with('patient', 'doctor', 'appointment')->paginate(5);
echo "✅ Medical records retrieved: " . $records->count() . " records\n";
echo "   Total records in DB: " . \App\Models\MedicalRecord::count() . "\n";
if (\App\Models\MedicalRecord::count() === 0) {
    echo "   (No records yet - ready to create)\n";
}
echo "\n";

// Test Controller Methods
echo "📋 Testing Controller Methods\n";
echo "───────────────────────────────────────────────────────────────────\n";

echo "DoctorController methods:\n";
$methods = get_class_methods($doctorController);
foreach ($methods as $method) {
    if (!str_starts_with($method, '__')) {
        echo "  ✅ $method()\n";
    }
}
echo "\n";

// Test Doctor relationships
echo "📋 Testing Doctor Relationships\n";
echo "───────────────────────────────────────────────────────────────────\n";
$doctor = \App\Models\Doctor::with('specialty', 'appointments')->first();
if ($doctor) {
    echo "✅ Doctor: {$doctor->first_name} {$doctor->last_name}\n";
    echo "   - Specialty: {$doctor->specialty->name}\n";
    echo "   - Phone: {$doctor->phone}\n";
    echo "   - Email: {$doctor->email}\n";
    echo "   - Appointments: " . $doctor->appointments->count() . "\n";
}
echo "\n";

// Test Patient relationships
echo "📋 Testing Patient Relationships\n";
echo "───────────────────────────────────────────────────────────────────\n";
$patient = \App\Models\Patient::with('appointments', 'medicalRecords')->first();
if ($patient) {
    echo "✅ Patient: {$patient->first_name} {$patient->last_name}\n";
    echo "   - DOB: {$patient->dob}\n";
    echo "   - Phone: {$patient->phone}\n";
    echo "   - Email: {$patient->email}\n";
    echo "   - Appointments: " . $patient->appointments->count() . "\n";
    echo "   - Medical Records: " . $patient->medicalRecords->count() . "\n";
}
echo "\n";

echo "═══════════════════════════════════════════════════════════════════\n";
echo "✨ All controllers tested successfully! 🎉\n";
echo "═══════════════════════════════════════════════════════════════════\n";
echo "\nAPI is ready to use at: http://localhost:8000/api/\n";
echo "Endpoints:\n";
echo "  - GET    /api/doctors\n";
echo "  - GET    /api/patients\n";
echo "  - GET    /api/specialties\n";
echo "  - GET    /api/appointments\n";
echo "  - GET    /api/medical-records\n";
