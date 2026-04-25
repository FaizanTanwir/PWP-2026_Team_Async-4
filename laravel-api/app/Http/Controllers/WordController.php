<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\Request;

class WordController extends Controller
{
    /**
     * Update Word
     *
     * Modify a word's term, lemma, or translation globally.
     * @status 200 { "id": 1, "term": "koira", "lemma": "koira", "translation": "dog" }
     * @status 401 { "message": "Unauthenticated." }
     * @status 403 { "message": "User does not have the right roles." }
     * @status 404 { "message": "Word not found." }
     * @status 422 { "message": "The term field must be a string.", "errors": { "term": ["The term field must be a string."] } }
     */
    public function update(Request $request, Word $word)
    {
        // Validation: term is required if provided, others are optional
        $validated = $request->validate([
            'term' => 'sometimes|string|max:255',
            'lemma' => 'sometimes|nullable|string|max:255',
            'translation' => 'sometimes|nullable|string|max:255',
        ]);

        $word->update($validated);

        return response()->json($word);
    }

    /**
     * Delete Word
     *
     * Remove a word from the global dictionary. This action is destructive.
     * @status 204
     * @status 403 { "message": "User does not have the right roles." }
     * @status 404 { "message": "Word not found." }
     */
    public function destroy(Word $word)
    {
        $word->delete();

        return response()->json(null, 204);
    }
}
