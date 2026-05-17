<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\Language;
use App\Models\User;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CourseController extends Controller
{
    /**
     * List Courses by Language
     *
     * Retrieve all courses where the specified language is the source language.
     * Includes metadata for target and source languages, units and the assigned teacher.
     */
    #[Response(404, 'Language not found.', type: 'array{message: string}')]
    public function index(Language $language)
    {
        $courses = $language->coursesAsSource()
            ->with(['sourceLanguage', 'targetLanguage', 'teacher', 'units'])
            ->get();

        return CourseResource::collection($courses);
    }

    /**
     * Create Course
     *
     * Create a new course under the specified source language.
     * The authenticated teacher is automatically assigned as the creator.
     *
     * @status 201 { "id": 1, "title": "Finnish 101", "source_language_id": 1, "target_language_id": 2 }
     * @status 401 { "message": "Unauthenticated." }
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Language not found', type: 'array{message: string}')]
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

        // Load the same relationships used in your show method/Resource
        $course->load(['sourceLanguage', 'targetLanguage', 'teacher', 'units']);

        // Return via CourseResource to ensure consistent JSON formatting
        return (new CourseResource($course))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * View Course
     *
     * Get detailed information about a course, including its units.
     */
    #[Response(404, 'Course not found', type: 'array{message: string}')]
    public function show(Course $course)
    {
        return new CourseResource($course->load(['sourceLanguage', 'targetLanguage', 'teacher', 'units']));
    }

    /**
     * Update Course
     *
     * Modify an existing course. Only the creator or an admin can perform this.
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Course not found', type: 'array{message: string}')]
    public function update(Request $request, Course $course)
    {
        $this->authorizeOwnership($course);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'source_language_id' => 'sometimes|exists:languages,id',
            'target_language_id' => 'sometimes|exists:languages,id',
        ]);

        $course->update($validated);

        $course->load(['sourceLanguage', 'targetLanguage', 'teacher', 'units']);

        return new CourseResource($course);
    }

    /**
     * Delete Course
     *
     * Remove a course and its associated content permanently.
     *
     * @status 204 No Content
     * @status 401 { "message": "Unauthenticated." }
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Course not found', type: 'array{message: string}')]
    public function destroy(Course $course)
    {
        $this->authorizeOwnership($course);

        $course->delete();

        return response()->json(['message' => 'Course deleted'], 204);
    }

    /**
     * @throws AccessDeniedHttpException
     */
    private function authorizeOwnership(Course $course): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Admin can do everything. Teacher can only touch their own.
        if ($user->hasRole(UserRole::ADMIN->value)) {
            return;
        }

        if ($course->created_by_id !== $user->id) {
            // Throwing the specific Exception instead of using abort()
            throw new AccessDeniedHttpException('You do not own this course.');
        }
    }
}
