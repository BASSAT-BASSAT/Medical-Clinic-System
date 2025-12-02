<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    protected $table = 'medical_records';
    protected $primaryKey = 'record_id';
    public $timestamps = true;

    protected $fillable = ['patient_id', 'doctor_id', 'appointment_id', 'record_date', 'diagnosis', 'prescription', 'notes'];

    protected $casts = [
        'record_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'doctor_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }
}
