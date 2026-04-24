<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\UnitResource;
use App\Models\Course;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::with('course.teacher')->get();
        return UnitResource::collection($units);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Check if the teacher owns the course they are adding a unit to
        $course = Course::findOrFail($validated['course_id']);
        $this->authorizeCourseOwnership($course);

        $unit = Unit::create($validated);
        return response()->json(new UnitResource($unit->load('course.teacher')), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        return new UnitResource($unit->load('course.teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $this->authorizeCourseOwnership($unit->course);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'course_id' => 'sometimes|exists:courses,id',
        ]);

        $unit->update($validated);
        return response()->json($unit);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $this->authorizeCourseOwnership($unit->course);

        $unit->delete();
        return response()->json(null, 204);
    }

    /**
     * Reusable ownership check
     */
    private function authorizeCourseOwnership(Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {
            return;
        }

        if ($course->created_by_id !== $user->id) {
            abort(403, 'You do not have permission to manage units for this course.');
        }
    }
}
