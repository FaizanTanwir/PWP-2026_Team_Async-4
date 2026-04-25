<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\Request;

class WordController extends Controller
{
    /**
     * Update vocabulary metadata.
     * * Useful for correcting translations or adding lemmas after initial sentence creation.
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
     * Delete a word from the global dictionary.
     * * * Warning: This will detach the word from all associated sentences.
     */
    public function destroy(Word $word)
    {
        $word->delete();

        return response()->json(null, 204);
    }
}
