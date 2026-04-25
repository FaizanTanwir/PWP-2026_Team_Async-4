<?php

namespace App\Http\Controllers;

use App\Http\Resources\LanguageResource;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * List all supported languages.
     * * Get a collection of languages available for course creation (Source/Target).
     */
    public function index()
    {
        return LanguageResource::collection(Language::all());
    }

    /**
     * Add a new language.
     * * * Restricted to Admin users via middleware.
     */
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
     * Get specific language details.
     */
    public function show(Language $language)
    {
        return new LanguageResource($language);
    }

    /**
     * Update language metadata.
     */
    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:languages,name,' . $language->id,
            'code' => 'sometimes|required|string|max:10|unique:languages,code,' . $language->id,
        ]);

        $language->update($validated);

        return new LanguageResource($language);
    }

    /**
     * Delete a language.
     * * * Warning: This may affect courses using this language.
     */
    public function destroy(Language $language)
    {
        $language->delete();

        return response()->json(null, 204);
    }
}
