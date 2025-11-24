<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function isAdmin() {
    return $this->role === 'admin';
    }

    public function isDoctor() {
    return $this->role === 'doctor';
    }

    public function isPatient() {
    return $this->role === 'patient';
    }
    
    public function doctor() {
    return $this->hasOne(Doctor::class);
    }

    public function patient() {
    return $this->hasOne(Patient::class);
    }

       
}
