<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

$doctors = \App\Models\Doctor::with('availability')->get();
foreach ($doctors as $doc) {
    echo "Doctor ID: {$doc->doctor_id}, Name: {$doc->first_name}, Specialty: {$doc->specialty_id}, Has Availability: " . $doc->availability->count() . "\n";
    if ($doc->availability->count() > 0) {
        foreach ($doc->availability as $avail) {
            echo "  - {$avail->day_of_week}: {$avail->start_time}-{$avail->end_time}\n";
        }
    }
}
