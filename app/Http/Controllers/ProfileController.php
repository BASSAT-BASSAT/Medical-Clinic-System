<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Don't use Hash::make() - User model has 'password' => 'hashed' cast
        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Delete the user's account and all related data.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        try {
            // Delete patient-related data
            if ($user->patient) {
                $patient = $user->patient;
                
                // Delete appointments first
                Appointment::where('patient_id', $patient->patient_id)->delete();
                
                // Delete medical records
                MedicalRecord::where('patient_id', $patient->patient_id)->delete();
                
                // Delete notifications related to this patient
                Notification::where('patient_id', $patient->patient_id)->delete();
                
                // Delete patient
                $patient->delete();
            }

            // Delete doctor-related data
            if ($user->doctor) {
                $doctor = $user->doctor;
                
                // Delete appointments where this doctor is assigned
                Appointment::where('doctor_id', $doctor->doctor_id)->delete();
                
                // Delete availability records
                $doctor->availability()->delete();
                
                // Delete notifications related to this doctor
                Notification::where('doctor_id', $doctor->doctor_id)->delete();
                
                // Delete doctor
                $doctor->delete();
            }

            // Log out and delete user
            Auth::logout();
            $user->delete();

            // Invalidate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/')->with('status', 'account-deleted');
        } catch (\Exception $e) {
            // Log error and return back with message
            \Log::error('Account deletion error: ' . $e->getMessage());
            return Redirect::route('profile.edit')->with('error', 'Error deleting account: ' . $e->getMessage());
        }
    }
}

