<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Sentence;
use App\Models\Submission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get Dashboard Statistics
     *
     * Retrieve a summary of activity and performance metrics.
     * The response structure dynamically changes based on the authenticated user's role.
     * @status 200 (Teacher) {
     * "sentences_count": 42,
     * "total_submissions": 150,
     * "avg_accuracy": 0.85
     * }
     * @status 200 (Student) {
     * "units_completed": 5,
     * "my_avg_accuracy": 0.92
     * }
     * @status 401 { "message": "Unauthenticated." }
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // If Teacher: Stats for the content they CREATED
        if ($user->hasRole(UserRole::TEACHER->value)) {
            return response()->json([
                'sentences_count' => Sentence::where('user_id', $user->id)->count(),
                'total_submissions' => Submission::whereHas('unit', function($q) use ($user) {
                    $q->whereHas('course', function($sq) use ($user) {
                        $sq->where('created_by_id', $user->id);
                    });
                })->count(),
                'avg_accuracy' => Submission::whereHas('unit.course', fn($q) => $q->where('created_by_id', $user->id))
                                    ->avg('accuracy') ?? 0
            ]);
        }

        // If Student: Stats for their own PROGRESS
        return response()->json([
            'units_completed' => Submission::where('user_id', $user->id)
                                ->distinct('unit_id')
                                ->count('unit_id'),
            'my_avg_accuracy' => $user->submissions()->avg('accuracy') ?? 0,
        ]);
    }
}
