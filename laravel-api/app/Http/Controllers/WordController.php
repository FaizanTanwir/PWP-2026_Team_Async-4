<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\Request;
use Dedoc\Scramble\Attributes\Response;

class WordController extends Controller
{
    /**
     * Update Word
     *
     * Modify a word's term, lemma, or translation globally.
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Word not found', type: 'array{message: string}')]
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
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Word not found', type: 'array{message: string}')]
    public function destroy(Word $word)
    {
        $word->delete();

        return response()->json(['message' => 'Word deleted'], 204);
    }
}
