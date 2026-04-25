<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    /**
     * Display a listing of submissions for a specific unit.
     */
    public function index(Unit $unit)
    {
        // Teachers can see all; students only see their own
        $query = Submission::where('unit_id', $unit->id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('student')) {
            $query->where('user_id', $user->id);
        }

        return response()->json($query->latest()->get());
    }

    /**
     * Store a newly created submission in storage.
     */
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

        return response()->json([
            'message' => 'Result recorded successfully',
            'data'    => $submission,
            'passed'  => $submission->is_passed // Using your model's attribute
        ], 201);
    }

    /**
     * Display the specified submission.
     */
    public function show(Submission $submission)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('student') && $submission->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($submission->load(['user', 'unit']));
    }
}
