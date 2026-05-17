<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\UnitResource;
use App\Models\Course;
use App\Models\Unit;
use App\Models\User;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    /**
     * List Units
     *
     * Retrieve all units across all courses.
     */
    #[Response(404, 'Course not found.')]
    public function index(Course $course)
    {
        $units = $course->units()
            ->with(['sentences', 'course.teacher'])
            ->get();

        return UnitResource::collection($units);
    }

    /**
     * Create Unit
     *
     * Add a new learning unit to a specific course.
     * Access is restricted to the course owner or administrators.
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Course not found', type: 'array{message: string}')]
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
     */
    #[Response(404, 'Unit not found', type: 'array{message: string}')]
    public function show(Unit $unit)
    {
        return new UnitResource($unit->load('course.teacher'));
    }

    /**
     * Update Unit
     *
     * Change title or parent course. Requires ownership of the course.
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Unit not found', type: 'array{message: string}')]
    public function update(Request $request, Unit $unit)
    {
        $this->authorizeCourseOwnership($unit->course);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'course_id' => 'sometimes|exists:courses,id',
        ]);

        $unit->update($validated);

        $unit->load('course.teacher');

        return new UnitResource($unit);
    }

    /**
     * Delete Unit
     *
     * Permanently remove a unit and its sentences.
     *
     * @status 204
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Unit not found', type: 'array{message: string}')]
    public function destroy(Unit $unit)
    {
        $this->authorizeCourseOwnership($unit->course);

        $unit->delete();

        return response()->json(['message' => 'Unit deleted'], 204);
    }

    private function authorizeCourseOwnership(Course $course)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {
            return;
        }

        if ($course->created_by_id !== $user->id) {
            abort(403, 'You do not have permission to manage units for this course.');
        }
    }
}
