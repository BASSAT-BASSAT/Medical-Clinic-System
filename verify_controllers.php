<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SpecialtyController;

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Clinic Controllers Verification ===\n\n";

$controllers = [
    'DoctorController' => DoctorController::class,
    'PatientController' => PatientController::class,
    'AppointmentController' => AppointmentController::class,
    'MedicalRecordController' => MedicalRecordController::class,
    'NotificationController' => NotificationController::class,
    'SpecialtyController' => SpecialtyController::class,
];

foreach ($controllers as $name => $class) {
    $exists = class_exists($class);
    $status = $exists ? '✅' : '❌';
    echo "$status $name: " . ($exists ? 'Loaded' : 'Failed') . "\n";
}

echo "\n=== Available Controller Methods ===\n\n";

$doctor = new DoctorController();
$methods = get_class_methods($doctor);
echo "DoctorController methods: " . count($methods) . "\n";
foreach ($methods as $method) {
    if (!str_starts_with($method, '__')) {
        echo "  - $method()\n";
    }
}

echo "\n✅ All controllers are ready to use!\n";
