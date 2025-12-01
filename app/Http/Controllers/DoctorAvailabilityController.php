<?php

namespace App\Http\Controllers;

use App\Models\DoctorAvailability;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorAvailabilityController extends Controller
{
    /**
     * Get availability for a specific doctor
     */
    public function index($doctorId)
    {
        $doctor = Doctor::findOrFail($doctorId);
        $availability = $doctor->availability()->orderBy('day_of_week')->get();
        
        return response()->json([
            'doctor_id' => $doctorId,
            'availability' => $availability,
        ]);
    }

    /**
     * Set or update availability for a doctor
     */
    public function store(Request $request)
    {
        $data = $request->all();
        
        // Ensure time format is H:i:s
        if (isset($data['start_time']) && strlen($data['start_time']) === 5) {
            $data['start_time'] .= ':00';
        }
        if (isset($data['end_time']) && strlen($data['end_time']) === 5) {
            $data['end_time'] .= ':00';
        }

        $validator = Validator::make($data, [
            'doctor_id' => 'required|exists:doctors,doctor_id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s',
            'is_available' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Check if end_time is before start_time (indicating next-day shift like 5pm to 1am)
        // This is a valid scenario for overnight shifts
        $startSeconds = strtotime('00:00:00 ' . $validated['start_time']) - strtotime('00:00:00');
        $endSeconds = strtotime('00:00:00 ' . $validated['end_time']) - strtotime('00:00:00');
        
        if ($endSeconds < $startSeconds) {
            // This is an overnight shift - mark it so the slot generation knows to handle it
            $validated['is_overnight'] = true;
        }

        // Check if availability already exists for this day
        $existing = DoctorAvailability::where('doctor_id', $validated['doctor_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->first();

        if ($existing) {
            $existing->update($validated);
            return response()->json($existing);
        }

        $availability = DoctorAvailability::create($validated);
        return response()->json($availability, 201);
    }

    /**
     * Update doctor availability
     */
    public function update(Request $request, $id)
    {
        $availability = DoctorAvailability::findOrFail($id);

        $validated = $request->validate([
            'start_time' => 'sometimes|date_format:H:i:s',
            'end_time' => 'sometimes|date_format:H:i:s|after:start_time',
            'is_available' => 'sometimes|boolean',
        ]);

        $availability->update($validated);
        return response()->json($availability);
    }

    /**
     * Delete availability
     */
    public function destroy($id)
    {
        $availability = DoctorAvailability::findOrFail($id);
        $availability->delete();
        return response()->json(['message' => 'Availability deleted successfully']);
    }

    /**
     * Get all availability for a specific day
     */
    public function byDay($doctorId, $dayOfWeek)
    {
        $availability = DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$availability) {
            return response()->json(['message' => 'No availability set for this day'], 404);
        }

        return response()->json($availability);
    }

    /**
     * Set bulk availability for a doctor (all days at once)
     */
    public function bulkSet(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,doctor_id',
            'availability' => 'required|array',
            'availability.*.day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'availability.*.start_time' => 'required|date_format:H:i:s',
            'availability.*.end_time' => 'required|date_format:H:i:s',
            'availability.*.is_available' => 'sometimes|boolean',
        ]);

        $doctor_id = $validated['doctor_id'];
        
        // Delete existing availability for this doctor
        DoctorAvailability::where('doctor_id', $doctor_id)->delete();

        // Create new availability records
        $created = [];
        foreach ($validated['availability'] as $slot) {
            $slot['doctor_id'] = $doctor_id;
            $slot['is_available'] = $slot['is_available'] ?? true;
            $created[] = DoctorAvailability::create($slot);
        }

        return response()->json([
            'message' => 'Availability updated successfully',
            'availability' => $created,
        ], 201);
    }
}
