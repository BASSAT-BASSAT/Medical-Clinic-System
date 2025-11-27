<?php

namespace App\Models;

use App\Models\User;
use App\Models\Specialty;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\DoctorAvailability;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doctor extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'doctor_id';
    public $timestamps = true;

    protected $fillable = ['user_id', 'first_name', 'last_name', 'specialty_id', 'phone', 'email'];

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class, 'specialty_id', 'specialty_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id', 'doctor_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id', 'doctor_id');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(DoctorAvailability::class, 'doctor_id', 'doctor_id');
    }

    public function user() : BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

}
