<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TranslationService
{
    protected string $url;

    public function __construct()
    {
        // Use the container name 'linguist_translator' if running inside Docker
        // or 'localhost' if running the test/artisan command locally.
        $this->url = config('services.libretranslate.url');
    }

    /**
     * Translate text from target language to source language.
     */
    public function translate(string $text, string $from, string $to): ?string
    {
        try {
            $response = Http::post($this->url, [
                'q' => $text,
                'source' => $from,
                'target' => $to,
                'alternatives' => 3,
                'format' => 'text'
            ]);

            return $response->json('translatedText');
        } catch (\Exception $e) {
            Log::error("Translation failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tokenize sentence into unique words.
     */
    public function tokenize(string $text): array
    {
        // Remove punctuation and split by spaces
        $cleanText = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        return collect(explode(' ', $cleanText))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
}
