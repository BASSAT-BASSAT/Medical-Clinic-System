<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Doctor;

class DoctorAvailability extends Model
{
    protected $table = 'doctor_availability';
    protected $primaryKey = 'availability_id';
    public $timestamps = true;

    protected $fillable = ['doctor_id', 'day_of_week', 'start_time', 'end_time', 'is_available', 'is_overnight'];

    protected $casts = [
        'is_available' => 'boolean',
        'is_overnight' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'doctor_id');
    }

    /**
     * Get availability for a specific day
     */
    public static function getAvailability($doctorId, $dayOfWeek)
    {
        return static::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();
    }
}
