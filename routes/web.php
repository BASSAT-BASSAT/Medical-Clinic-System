<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'doctor') {
        return redirect()->route('doctor.dashboard');
    } elseif ($user->role === 'patient') {
        return redirect()->route('patient.dashboard');
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth','role:patient'])->group(function(){
    Route::get('/patient/dashboard',[PatientController::class,'dashboard'])->name('patient.dashboard');
    Route::get('/patient/appointments/book',[PatientController::class,'bookAppointment'])->name('patient.appointments.book');
    Route::get('/patient/records',[PatientController::class,'medicalRecordsView'])->name('patient.records');
    Route::get('/patient/notifications',[PatientController::class,'notifications'])->name('patient.notifications');
});

Route::middleware(['auth','role:doctor'])->group(function(){
    Route::get('/doctor/dashboard',[DoctorController::class,'dashboard'])->name('doctor.dashboard');
    Route::get('/doctor/reports',[DoctorController::class,'reports'])->name('doctor.reports');
    Route::get('/doctor/patients',[DoctorController::class,'patients'])->name('doctor.patients');
});

Route::middleware(['auth','role:admin'])->group(function(){
    Route::get('/admin/dashboard',[AdminController::class,'dashboard'])->name('admin.dashboard');
});

require __DIR__.'/auth.php';
