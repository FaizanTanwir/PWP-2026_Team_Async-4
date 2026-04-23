<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['sourceLanguage', 'targetLanguage', 'teacher'])->get();
        return CourseResource::collection($courses);
    }

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

    public function show(Course $course)
    {
        return new CourseResource($course->load(['sourceLanguage', 'targetLanguage', 'teacher', 'units']));
    }

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

    public function destroy(Course $course)
    {
        $this->authorizeOwnership($course);

        $course->delete();
        return response()->json(['message' => 'Course deleted'], 204);
    }

    /**
     * Private helper to check if the user is an Admin or the Owner.
     */
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
