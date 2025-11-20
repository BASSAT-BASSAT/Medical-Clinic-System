<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'doctor_id';
    public $timestamps = true;

    protected $fillable = ['first_name', 'last_name', 'specialty_id', 'phone', 'email'];

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
}
