<?php

namespace App\Models;

// Add these imports
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Your existing methods
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