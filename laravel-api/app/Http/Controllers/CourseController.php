<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * List Courses
     *
     * Retrieve a collection of all language courses.
     * @status 200 { "data": [ { "id": 1, "title": "Finnish for Beginners", "teacher": {...} } ] }
     */
    public function index()
    {
        $courses = Course::with(['sourceLanguage', 'targetLanguage', 'teacher'])->get();
        return CourseResource::collection($courses);
    }

    /**
     * Create Course
     *
     * Create a new course. The authenticated user is automatically assigned as the creator.
     * @status 201 { "id": 5, "title": "Urdu 101", "created_by_id": 1 }
     * @status 401 { "message": "Unauthenticated." }
     * @status 422 { "message": "The source language id field is required.", "errors": { "source_language_id": ["The selected source language id is invalid."] } }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'source_language_id' => 'required|exists:languages,id',
            'target_language_id' => 'required|exists:languages,id',
        ]);

        // Automatically set the teacher as the creator
        $validated['created_by_id'] = Auth::id();

        $course = Course::create($validated);
        return response()->json($course->load(['sourceLanguage', 'targetLanguage', 'teacher']), 201);
    }

    /**
     * View Course
     *
     * Get detailed information about a course, including its units.
     * @status 200 { "id": 1, "title": "Finnish", "units": [...] }
     * @status 404 { "message": "Record not found." }
     */
    public function show(Course $course)
    {
        return new CourseResource($course->load(['sourceLanguage', 'targetLanguage', 'teacher', 'units']));
    }

    /**
     * Update Course
     *
     * Modify an existing course. Only the creator or an admin can perform this.
     * @status 200 { "id": 1, "title": "Updated Finnish" }
     * @status 403 { "message": "You do not own this course." }
     * @status 422 { "errors": { "title": ["The title may not be greater than 255 characters."] } }
     */
    public function update(Request $request, Course $course)
    {
        $this->authorizeOwnership($course);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'source_language_id' => 'sometimes|exists:languages,id',
            'target_language_id' => 'sometimes|exists:languages,id',
        ]);

        $course->update($validated);
        return response()->json($course);
    }

    /**
     * Delete Course
     *
     * Remove a course and its associated content permanently.
     * @status 204
     * @status 403 { "message": "You do not own this course." }
     */
    public function destroy(Course $course)
    {
        $this->authorizeOwnership($course);

        $course->delete();
        return response()->json(['message' => 'Course deleted'], 204);
    }

    private function authorizeOwnership(Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Admin can do everything. Teacher can only touch their own.
        if ($user->hasRole(UserRole::ADMIN->value)) {
            return;
        }

        if ($course->created_by_id !== $user->id) {
            abort(403, 'You do not own this course.');
        }
    }
}
