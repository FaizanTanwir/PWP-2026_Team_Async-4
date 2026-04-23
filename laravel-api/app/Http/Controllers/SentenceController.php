<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Sentence;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SentenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Unit $unit)
    {
        return response()->json($unit->sentences);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text_target' => 'required|string',
            'text_source' => 'required|string',
            'unit_id' => 'required|exists:units,id',
        ]);

        $unit = Unit::findOrFail($validated['unit_id']);

        // Use the relationship to verify the teacher owns the parent course
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->id !== $unit->course->created_by_id && !$user->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }

        $sentence = Sentence::create([
            'text_target' => $validated['text_target'],
            'text_source' => $validated['text_source'],
            'unit_id'     => $validated['unit_id'],
            'user_id'     => Auth::id(),
        ]);

        return response()->json($sentence, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sentence $sentence)
    {
        $this->authorizeOwner($sentence);

        $validated = $request->validate([
            'text_target' => 'sometimes|string',
            'text_source' => 'sometimes|string',
        ]);

        $sentence->update($validated);
        return response()->json($sentence);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sentence $sentence)
    {
        $this->authorizeOwner($sentence);
        $sentence->delete();
        return response()->json(null, 204);
    }

    private function authorizeOwner(Sentence $sentence)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) return;

        if ($sentence->user_id !== $user->id) {
            abort(403, 'You do not own this sentence.');
        }
    }
}
