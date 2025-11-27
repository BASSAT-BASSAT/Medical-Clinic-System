<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use App\Models\Appointment;

echo "Appointments for Doctor 9 sorted by date:\n";
$apts = Appointment::where('doctor_id', 9)->orderBy('start_time')->get();
foreach ($apts as $apt) {
    echo "  Start: {$apt->start_time}, End: {$apt->end_time}\n";
}

echo "\nTrying to find conflicts for 2025-12-10 14:00:00 to 2025-12-10 15:00:00:\n";

// Check using the same logic as the controller
$start = "2025-12-10 14:00:00";
$end = "2025-12-10 15:00:00";

$conflict = Appointment::where('doctor_id', 9)
    ->where('status', '!=', 'cancelled')
    ->where(function ($query) use ($start, $end) {
        $query->whereBetween('start_time', [$start, $end])
            ->orWhereBetween('end_time', [$start, $end])
            ->orWhere(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
            });
    })
    ->get();

if ($conflict->count() > 0) {
    echo "CONFLICT FOUND:\n";
    foreach ($conflict as $c) {
        echo "  - ID {$c->appointment_id}: {$c->start_time} to {$c->end_time}\n";
    }
} else {
    echo "No conflicts\n";
}
