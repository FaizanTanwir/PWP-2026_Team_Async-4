<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * List Courses by Language
     *
     * Retrieve all courses where the specified language is the source language.
     * Includes metadata for target languages and the assigned teacher.
     * * @status 200 { "data": [ { "id": 1, "title": "Finnish 101", "target_language": {...} } ] }
     * @status 404 { "message": "Language not found." }
     */
    public function index(Language $language)
    {
        $courses = $language->coursesAsSource()
            ->with(['sourceLanguage', 'targetLanguage', 'teacher'])
            ->get();

        return CourseResource::collection($courses);
    }

    /**
     * Create Course
     * * Create a new course under the specified source language.
     * The authenticated teacher is automatically assigned as the creator.
     * * @status 201 { "id": 1, "title": "Finnish 101", "source_language_id": 1, "target_language_id": 2 }
     * @status 401 { "message": "Unauthenticated." }
     * @status 403 { "message": "User does not have the right roles." }
     * @status 422 { "message": "The target language id field is required.", "errors": { "target_language_id": ["The selected target language id is invalid."] } }
     */
    public function store(Request $request, Language $language)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_language_id' => 'required|exists:languages,id',
        ]);

        // Source language is inherited from the URL context
        $course = $language->coursesAsSource()->create([
            'title' => $validated['title'],
            'target_language_id' => $validated['target_language_id'],
            'created_by_id' => Auth::id(),
        ]);

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
