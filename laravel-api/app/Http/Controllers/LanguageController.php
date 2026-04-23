<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Display a listing of the languages.
     */
    public function index()
    {
        return response()->json(Language::all(), 200);
    }

    /**
     * Store a newly created language in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:languages,name',
            'code' => 'required|string|max:10|unique:languages,code',
        ]);

        $language = Language::create($validated);

        return response()->json($language, 201);
    }

    /**
     * Display the specified language.
     */
    public function show(Language $language)
    {
        return response()->json($language, 200);
    }

    /**
     * Update the specified language in storage.
     */
    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:languages,name,' . $language->id,
            'code' => 'sometimes|required|string|max:10|unique:languages,code,' . $language->id,
        ]);

        $language->update($validated);

        return response()->json($language, 200);
    }

    /**
     * Remove the specified language from storage.
     */
    public function destroy(Language $language)
    {
        $language->delete();

        return response()->json(null, 204);
    }
}
