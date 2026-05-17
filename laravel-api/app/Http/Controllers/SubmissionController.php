<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dedoc\Scramble\Attributes\Response;

class SubmissionController extends Controller
{
    /**
     * List Submissions
     *
     * Retrieve history of exercise attempts. Students are restricted to their own data. Teachers can view all attempts for a unit.
     */
    #[Response(401, 'Unauthenticated')]
    #[Response(404, 'Unit not found', type: 'array{message: string}')]
    public function index(Unit $unit)
    {
        // Teachers can see all; students only see their own
        $query = Submission::with('user')->where('unit_id', $unit->id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('student')) {
            $query->where('user_id', $user->id);
        }

        return SubmissionResource::collection($query->latest()->get());
    }

    /**
     * Store Submission
     *
     * Record the result of a quiz attempt. Accuracy is calculated server-side.
     */
    #[Response(404, 'Unit not found', type: 'array{message: string}')]
    public function store(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'type'            => 'required|string', // Validated via Enum cast in model
            'question_text'   => 'required|string',
            'provided_answer' => 'required|string',
            'correct_answer'  => 'required|string',
        ]);

        // Calculate accuracy (Case-insensitive comparison)
        $isCorrect = trim(strtolower($validated['provided_answer'])) === trim(strtolower($validated['correct_answer']));
        $accuracy = $isCorrect ? 1.0 : 0.0;

        $submission = Submission::create([
            'user_id'         => Auth::id(),
            'unit_id'         => $unit->id,
            'type'            => $validated['type'],
            'question_text'   => $validated['question_text'],
            'provided_answer' => $validated['provided_answer'],
            'correct_answer'  => $validated['correct_answer'],
            'accuracy'        => $accuracy,
        ]);

        return (new SubmissionResource($submission))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * View Submission
     *
     * Retrieve a specific attempt. Students can only access their own submissions.
     */
    #[Response(403, 'Unauthorized', type: 'array{message: string}')]
    #[Response(404, 'Not Found', type: 'array{message: string}')]
    public function show(Submission $submission)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('student') && $submission->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new SubmissionResource($submission->load(['user', 'unit']));
    }
}
