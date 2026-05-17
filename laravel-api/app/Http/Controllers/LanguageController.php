<?php

namespace App\Http\Controllers;

use App\Http\Resources\LanguageResource;
use App\Models\Language;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * List Languages
     *
     * Retrieve all languages available in the system.
     */
    public function index()
    {
        return LanguageResource::collection(Language::all());
    }

    /**
     * Create Language
     *
     * Add a new language to the system. Restricted to Admin users.
     */
    #[Response(401, 'Unauthenticated')]
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:languages,name',
            'code' => 'required|string|max:10|unique:languages,code',
        ]);

        $language = Language::create($validated);

        return (new LanguageResource($language))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * View Language
     *
     * Retrieve metadata for a specific language ID.
     */
    #[Response(404, 'Language not found', type: 'array{message: string}')]
    public function show(Language $language)
    {
        return new LanguageResource($language);
    }

    /**
     * Update Language
     *
     * Modify existing language metadata.
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Language not found', type: 'array{message: string}')]
    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:languages,name,'.$language->id,
            'code' => 'sometimes|required|string|max:10|unique:languages,code,'.$language->id,
        ]);

        $language->update($validated);

        return new LanguageResource($language);
    }

    /**
     * Delete Language
     *
     * Remove a language. Warning: This may cause orphaned courses.
     */
    #[Response(403, 'Forbidden', type: 'array{message: string}')]
    #[Response(404, 'Language not found', type: 'array{message: string}')]
    #[Response(422, 'Cannot delete language associated with existing courses.')]
    public function destroy(Language $language)
    {
        if ($language->coursesAsSource()->exists() || $language->coursesAsTarget()->exists()) {
            return response()->json([
                'message' => 'Cannot delete language associated with existing courses.',
            ], 422);
        }

        $language->delete();

        return response()->json(['message' => 'Language deleted'], 204);
    }
}

/**
 * Delete Language
 *
 * Remove a language. Warning: This may cause orphaned courses.
 *
 * @status 204 No Content
 * @status 403 { "message": "User does not have the right roles." }
 * @status 422 { "message": "Cannot delete language associated with existing courses." }
 */
