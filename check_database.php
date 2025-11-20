<?php

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Notification;

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Medical Clinic Database Summary ===\n\n";

echo "Specialties: " . Specialty::count() . "\n";
echo "Doctors: " . Doctor::count() . "\n";
echo "Patients: " . Patient::count() . "\n";
echo "Appointments: " . Appointment::count() . "\n";
echo "Medical Records: " . MedicalRecord::count() . "\n";
echo "Notifications: " . Notification::count() . "\n\n";

echo "=== Sample Data ===\n\n";

echo "First 3 Doctors with Specialties:\n";
Doctor::with('specialty')->limit(3)->get()->each(function ($doctor) {
    echo "  - {$doctor->first_name} {$doctor->last_name} ({$doctor->specialty->name})\n";
});

echo "\nFirst 3 Patients:\n";
Patient::limit(3)->get()->each(function ($patient) {
    echo "  - {$patient->first_name} {$patient->last_name} (DOB: {$patient->dob})\n";
});

echo "\nSpecialties:\n";
Specialty::all()->each(function ($specialty) {
    echo "  - {$specialty->name}\n";
});

echo "\n✅ Database is working correctly!\n";
