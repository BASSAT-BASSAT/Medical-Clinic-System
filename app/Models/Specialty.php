<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    protected $table = 'specialties';
    protected $primaryKey = 'specialty_id';
    public $timestamps = true;

    protected $fillable = ['name'];

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'specialty_id', 'specialty_id');
    }
}
