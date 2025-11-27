<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\User;

class Patient extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'patient_id';
    public $timestamps = true;

    protected $fillable = ['user_id', 'first_name', 'last_name', 'dob', 'phone', 'email'];

    protected $casts = [
        'dob' => 'date',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id', 'patient_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'patient_id', 'patient_id');
    }

    public function user()  : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
