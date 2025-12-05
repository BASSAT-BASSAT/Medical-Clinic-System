<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        Log::info('Password update attempt', ['user_id' => $request->user()->id, 'email' => $request->user()->email]);
        
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Don't use Hash::make() here - the User model has 'password' => 'hashed' cast
        // which automatically hashes the password when set
        $request->user()->update([
            'password' => $validated['password'],
        ]);

        Log::info('Password updated successfully', ['user_id' => $request->user()->id]);

        return back()->with('status', 'password-updated');
    }
}
