<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of all specialties
     */
    public function index()
    {
        $specialties = Specialty::with('doctors')->get();
        return response()->json($specialties);
    }

    /**
     * Store a newly created specialty
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:specialties,name',
        ]);

        $specialty = Specialty::create($validated);
        return response()->json($specialty, 201);
    }

    /**
     * Display the specified specialty
     */
    public function show($id)
    {
        $specialty = Specialty::with('doctors')->findOrFail($id);
        return response()->json($specialty);
    }

    /**
     * Update the specified specialty
     */
    public function update(Request $request, $id)
    {
        $specialty = Specialty::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100|unique:specialties,name,' . $id . ',specialty_id',
        ]);

        $specialty->update($validated);
        return response()->json($specialty);
    }

    /**
     * Delete the specified specialty
     */
    public function destroy($id)
    {
        $specialty = Specialty::findOrFail($id);
        $specialty->delete();
        return response()->json(['message' => 'Specialty deleted successfully']);
    }
}
