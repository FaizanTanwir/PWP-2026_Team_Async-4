<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\SentenceResource;
use App\Models\Sentence;
use App\Models\Unit;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SentenceController extends Controller
{
    /**
     * List all sentences for a specific unit.
     * * Returns sentences with their associated word tokens.
     */
    public function index(Unit $unit)
    {
        $sentences = $unit->sentences()->with('words')->get();
        return SentenceResource::collection($sentences);
    }

    /**
     * Add a sentence and its word tokens.
     * * This method handles tokenization by syncing words to the sentence.
     * * If a word 'term' already exists in the global dictionary, it is reused.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text_target' => 'required|string',
            'text_source' => 'required|string',
            'unit_id'     => 'required|exists:units,id',

            // Use 'min:1' to ensure the array isn't empty
            'words'       => 'required|array|min:1',

            'words.*.term'        => 'required|string',
            'words.*.translation' => 'nullable|string',
            'words.*.lemma'       => 'nullable|string',
        ], [
            // 2. Add Custom Error Messages
            'words.required' => 'The sentence must have at least one word associated with it.',
            'words.array'    => 'The words field must be a valid list of word objects.',
            'words.min'      => 'You must provide at least one word for tokenization.',
        ]);

        $unit = Unit::findOrFail($validated['unit_id']);

        // Use the relationship to verify the teacher owns the parent course
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->id !== $unit->course->created_by_id && !$user->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }

        // 1. Create the Sentence
        $sentence = Sentence::create([
            'text_target' => $validated['text_target'],
            'text_source' => $validated['text_source'],
            'unit_id'     => $validated['unit_id'],
            'user_id'     => Auth::id(),
        ]);

        // 2. Process Words & Sync to Pivot Table
        $wordIds = [];
        foreach ($validated['words'] as $wordData) {
            // We use updateOrCreate so we don't duplicate global words
            $word = Word::updateOrCreate(
                ['term' => $wordData['term']],
                [
                    'lemma' => $wordData['lemma'] ?? null,
                    'translation' => $wordData['translation'] ?? null
                ]
            );
            $wordIds[] = $word->id;
        }

        // 3. Link sentences and words in the sentence_word table
        $sentence->words()->sync($wordIds);

        return (new SentenceResource($sentence->load('words')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update sentence text and/or its word tokens.
     * * * Note: Providing a 'words' array will replace all existing word links for this sentence.
     */
    public function update(Request $request, Sentence $sentence)
    {
        $this->authorizeOwner($sentence);

        $validated = $request->validate([
            'text_target' => 'sometimes|string',
            'text_source' => 'sometimes|string',
            // Validate words if they are being updated
            'words'       => 'sometimes|array|min:1',
            'words.*.term'        => 'required_with:words|string',
            'words.*.translation' => 'nullable|string',
            'words.*.lemma'       => 'nullable|string',
        ]);

        // 1. Update the sentence text
        $sentence->update($request->only(['text_target', 'text_source']));

        // 2. Sync words only if they were provided in the request
        if ($request->has('words')) {
            $wordIds = [];
            foreach ($request->words as $wordData) {
                $word = Word::updateOrCreate(
                    ['term' => $wordData['term']],
                    [
                        'lemma' => $wordData['lemma'] ?? null,
                        'translation' => $wordData['translation'] ?? null
                    ]
                );
                $wordIds[] = $word->id;
            }

            // sync() handles the "cleanup" automatically:
            // It adds new words and removes words no longer present in the array.
            $sentence->words()->sync($wordIds);
        }

        return (new SentenceResource($sentence->load('words')))
            ->response()
            ->setStatusCode(200); // Changed to 200 as 201 is for "Created"
    }

    /**
     * Delete a sentence and detach its word relations.
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
