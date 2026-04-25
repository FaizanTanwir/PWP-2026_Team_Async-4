<?php

namespace App\Http\Controllers;

use App\Http\Resources\LanguageResource;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * List Languages
     *
     * Retrieve all languages available in the system.
     * @status 200 { "data": [ { "id": 1, "name": "English", "code": "en" } ] }
     */
    public function index()
    {
        return LanguageResource::collection(Language::all());
    }

    /**
     * Create Language
     *
     * Add a new language to the system. Restricted to Admin users.
     * @status 201 { "id": 3, "name": "German", "code": "de" }
     * @status 401 { "message": "Unauthenticated." }
     * @status 403 { "message": "User does not have the right roles." }
     * @status 422 { "message": "The name has already been taken.", "errors": { "name": ["The name has already been taken."], "code": ["The code has already been taken."] } }
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
     * View Language
     *
     * Retrieve metadata for a specific language ID.
     * @status 200 { "id": 1, "name": "English", "code": "en" }
     * @status 404 { "message": "Record not found." }
     */
    public function show(Language $language)
    {
        return new LanguageResource($language);
    }

    /**
     * Update Language
     *
     * Modify existing language metadata.
     * @status 200 { "id": 1, "name": "English (UK)", "code": "en-GB" }
     * @status 403 { "message": "User does not have the right roles." }
     * @status 422 { "errors": { "name": ["The name has already been taken."] } }
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
     * Delete Language
     *
     * Remove a language. Warning: This may cause orphaned courses.
     * @status 204
     * @status 403 { "message": "User does not have the right roles." }
     */
    public function destroy(Language $language)
    {
        $language->delete();

        return response()->json(null, 204);
    }
}
