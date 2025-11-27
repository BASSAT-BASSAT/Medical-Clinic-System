<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';
    public $timestamps = true;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'type',
        'notification_type',
        'message',
        'is_sent',
        'sent_at',
        'recipient',
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'doctor_id');
    }

    /**
     * Mark notification as sent
     */
    public function markAsSent()
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);
    }

    /**
     * Get unsent notifications
     */
    public static function getUnsent()
    {
        return static::where('is_sent', false)->get();
    }

    /**
     * Get notifications by appointment
     */
    public static function getByAppointment($appointmentId)
    {
        return static::where('appointment_id', $appointmentId)->get();
    }

    /**
     * Get notifications by patient
     */
    public static function getByPatient($patientId)
    {
        return static::where('patient_id', $patientId)->orderBy('created_at', 'desc')->get();
    }
}
