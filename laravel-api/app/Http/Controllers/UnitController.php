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
     * List all units across all courses.
     * * Includes parent course and teacher metadata.
     */
    public function index()
    {
        $units = Unit::with('course.teacher')->get();
        return UnitResource::collection($units);
    }

    /**
     * Create a new unit within a course.
     * * Only the course owner (Teacher) or an Admin can add units.
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
     * View unit details and associated sentences.
     */
    public function show(Unit $unit)
    {
        return new UnitResource($unit->load('course.teacher'));
    }

    /**
     * Update unit title or move it to a different course.
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
     * Delete a unit and its associated content.
     */
    public function destroy(Unit $unit)
    {
        $this->authorizeCourseOwnership($unit->course);

        $unit->delete();
        return response()->json(null, 204);
    }

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
