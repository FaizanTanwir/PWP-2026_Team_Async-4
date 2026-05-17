<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\SentenceResource;
use App\Jobs\ProcessUnitFile;
use App\Models\Sentence;
use App\Models\Unit;
use App\Models\User;
use App\Models\Word;
use App\Services\TranslationService;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SentenceController extends Controller
{
    /**
     * List Sentences
     *
     * Retrieve all sentences for a given unit, including their tokenized words.
     */
    #[Response(404, 'Unit not found.')]
    public function index(Unit $unit)
    {
        $sentences = $unit->sentences()->with('words')->get();

        return SentenceResource::collection($sentences);
    }

    /**
     * Create Sentence & Tokenize Words
     *
     * Create a sentence within a unit and synchronize its word tokens.
     * Existing words in the dictionary are reused; new words are created automatically.
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Unit not found.')]
    public function store(Request $request, Unit $unit)
    {
        /** @var User $user */
        $user = Auth::user();

        // Verify teacher owns the course that this unit belongs to
        if ($user->id !== $unit->course->created_by_id && ! $user->hasRole('admin')) {
            abort(403, 'Unauthorized. You do not own the parent course.');
        }

        $validated = $request->validate([
            'text_target' => 'required|string',
            'text_source' => 'required|string',
            'words' => 'required|array|min:1',
            'words.*.term' => 'required|string',
            'words.*.translation' => 'nullable|string',
            'words.*.lemma' => 'nullable|string',
        ]);

        // 1. Create the Sentence through the unit relationship
        $sentence = $unit->sentences()->create([
            'text_target' => $validated['text_target'],
            'text_source' => $validated['text_source'],
            'user_id' => $user->id,
        ]);

        // 2. Process & Sync Words
        $wordIds = [];

        $targetLanguageId = $unit->course->target_language_id;

        foreach ($validated['words'] as $wordData) {
            $word = Word::updateOrCreate(
                // Array 1: Unique attributes to find the record (Search Criteria)
                ['term' => $wordData['term']],

                // Array 2: Attributes to set/update if found or created
                [
                    'lemma' => $wordData['lemma'] ?? null,
                    'translation' => $wordData['translation'] ?? null,
                    'language_id' => $targetLanguageId,
                ]
            );
            $wordIds[] = $word->id;
        }

        $sentence->words()->sync($wordIds);

        return (new SentenceResource($sentence->load('words')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update Sentence
     *
     * Modify sentence text or word tokens. Note: Syncing words replaces the entire set.
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Sentence not found', type: 'array{message: string}')]
    public function update(Request $request, Sentence $sentence)
    {
        $this->authorizeOwner($sentence);

        $validated = $request->validate([
            'text_target' => 'sometimes|string',
            'text_source' => 'sometimes|string',
            // Validate words if they are being updated
            'words' => 'sometimes|array|min:1',
            'words.*.term' => 'required_with:words|string',
            'words.*.translation' => 'nullable|string',
            'words.*.lemma' => 'nullable|string',
        ]);

        // 1. Update the sentence text
        $sentence->update($request->only(['text_target', 'text_source']));

        $targetLanguageId = $sentence->unit->course->target_language_id;
        // 2. Sync words only if they were provided in the request
        if ($request->has('words')) {
            $wordIds = [];
            foreach ($request->words as $wordData) {
                $word = Word::updateOrCreate(
                    ['term' => $wordData['term']],
                    [
                        'lemma' => $wordData['lemma'] ?? null,
                        'translation' => $wordData['translation'] ?? null,
                        'language_id' => $targetLanguageId,
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
     * Delete Sentence
     *
     * Remove a sentence and detach all word associations.
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Sentence not found', type: 'array{message: string}')]
    public function destroy(Sentence $sentence)
    {
        $this->authorizeOwner($sentence);
        $sentence->delete();

        return response()->json(['message' => 'Sentence deleted'], 204);
    }

    /**
     * Preview Sentence Translation & Tokenization
     *
     * Takes a raw string and uses the TranslationService to provide a
     * suggested translation and a breakdown of individual words.
     *
     * @status 200 {
     * "sentence": { "text_target": "Minä syön", "text_source": "I eat" },
     * "words": [ { "term": "Minä", "translation": "I", "lemma": null } ],
     * "meta": { "target_lang": "fi", "source_lang": "en" }
     * }
     * @status 401 { "message": "Unauthenticated." }
     * @status 404 { "message": "Unit not found." }
     * @status 422 { "message": "The text field is required." }
     */
    public function preview(Request $request, Unit $unit, TranslationService $translator)
    {
        $request->validate(['text' => 'required|string']);

        // Load the course and languages
        $course = $unit->course;
        $targetLang = $course->targetLanguage->code; // e.g., 'fi'
        $sourceLang = $course->sourceLanguage->code; // e.g., 'en'

        $targetText = $request->text;

        // 1. Translate the whole sentence using dynamic codes
        $sentenceTranslation = $translator->translate($targetText, $sourceLang, $targetLang);

        // 2. Tokenize and translate individual words
        $terms = $translator->tokenize($targetText);

        $words = [];
        foreach ($terms as $term) {
            $words[] = [
                'term' => $term,
                'translation' => $translator->translate($term, $sourceLang, $targetLang),
                'lemma' => null,
            ];
        }

        return response()->json([
            'sentence' => [
                'text_target' => $targetText,
                'text_source' => $sentenceTranslation,
            ],
            'words' => $words,
            'meta' => [
                'target_lang' => $targetLang,
                'source_lang' => $sourceLang,
            ],
        ]);
    }

    /**
     * Bulk Upload Sentences
     *
     * Upload a .txt file containing sentences (one per line).
     * The file is processed in the background: sentences are created,
     * tokenized, and translated automatically.
     *
     * @status 202 { "message": "Your file is being processed. Sentences will appear in the unit shortly." }
     * @status 401 { "message": "Unauthenticated." }
     * @status 403 { "message": "Unauthorized. You do not own the parent course." }
     * @status 404 { "message": "Unit not found." }
     * @status 422 { "message": "The file field is required.", "errors": { "file": ["The file must be a file of type: txt."] } }
     */
    public function upload(Request $request, Unit $unit)
    {
        /** @var User $user */
        $user = Auth::user();

        // Re-use your ownership logic
        if ($user->id !== $unit->course->created_by_id && ! $user->hasRole(UserRole::ADMIN->value)) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'file' => 'required|file|mimes:txt|max:2048', // Max 2MB
        ]);

        $content = file_get_contents($request->file('file')->getRealPath());

        // Dispatch the job to the queue
        ProcessUnitFile::dispatch($unit, $user, $content);

        return response()->json([
            'message' => 'Your file is being processed. Sentences will appear in the unit shortly.',
        ], 202); // 202 Accepted
    }

    /**
     * @throws AccessDeniedHttpException
     */
    private function authorizeOwner(Sentence $sentence): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Admin can do everything. Teacher can only touch their own.
        if ($user->hasRole(UserRole::ADMIN->value)) {
            return;
        }

        if ($sentence->user_id !== $user->id) {
            // Throwing the specific Exception instead of using abort()
            throw new AccessDeniedHttpException('You do not own this sentence.');
        }
    }
}
