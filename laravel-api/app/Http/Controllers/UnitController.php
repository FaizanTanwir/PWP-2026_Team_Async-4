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
     * List Units
     *
     * Retrieve all units across all courses.
     * @status 200 { "data": [ { "id": 1, "title": "Basics 1", "course": {...} } ] }
     */
    public function index()
    {
        $units = Unit::with('course.teacher')->get();
        return UnitResource::collection($units);
    }

    /**
     * Create Unit
     * * Add a new learning unit to a specific course.
     * Access is restricted to the course owner or administrators.
     * * @status 201 { "id": 5, "title": "Vocabulary Basics", "course_id": 1 }
     * @status 401 { "message": "Unauthenticated." }
     * @status 403 { "message": "You do not have permission to manage units for this course." }
     * @status 404 { "message": "Course not found." }
     * @status 422 { "message": "The title field is required." }
     */
    public function store(Request $request, Course $course)
{
    // Ownership check using the injected $course object
    $this->authorizeCourseOwnership($course);

    $validated = $request->validate([
        'title' => 'required|string|max:255',
    ]);

    $unit = $course->units()->create($validated);

    return response()->json(new UnitResource($unit->load('course.teacher')), 201);
}

    /**
     * View Unit
     *
     * Retrieve specific unit details.
     * @status 200 { "id": 1, "title": "Basics 1" }
     * @status 404 { "message": "Record not found." }
     */
    public function show(Unit $unit)
    {
        return new UnitResource($unit->load('course.teacher'));
    }

    /**
     * Update Unit
     *
     * Change title or parent course. Requires ownership of the course.
     * @status 200 { "id": 1, "title": "Revised Basics" }
     * @status 403 { "message": "You do not have permission to manage units for this course." }
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
     * Delete Unit
     *
     * Permanently remove a unit and its sentences.
     * @status 204
     * @status 403 { "message": "You do not have permission to manage units for this course." }
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
