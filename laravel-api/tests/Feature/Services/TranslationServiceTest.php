<?php

namespace Tests\Feature\Services;

use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    protected TranslationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure the config returns a value for the URL
        config(['services.libretranslate.url' => 'http://localhost:5000/translate']);
        $this->service = new TranslationService();
    }

    public function test_successfully_translates_text()
    {
        Http::fake([
            '*' => Http::response(['translatedText' => 'Hello world'], 200),
        ]);

        $result = $this->service->translate('Hei maailma', 'fi', 'en');

        $this->assertEquals('Hello world', $result);

        Http::assertSent(function ($request) {
            return $request['q'] === 'Hei maailma' &&
                   $request['source'] === 'fi' &&
                   $request['target'] === 'en';
        });
    }

    public function test_tokenizes_sentences_correctly()
    {
        $text = "Minä rakastan Oulua!";
        $tokens = $this->service->tokenize($text);

        $this->assertEquals(['Minä', 'rakastan', 'Oulua'], $tokens);
    }

    public function test_tokenizes_and_removes_duplicates()
    {
        $text = "moi moi moi";
        $tokens = $this->service->tokenize($text);

        $this->assertCount(1, $tokens);
        $this->assertEquals(['moi'], $tokens);
    }

    public function test_handles_complex_punctuation_during_tokenization()
    {
        $text = "Yliopisto, (Oulu) - hieno paikka.";
        $tokens = $this->service->tokenize($text);

        // Should strip commas, parentheses, hyphens, and periods
        $this->assertEquals(['Yliopisto', 'Oulu', 'hieno', 'paikka'], $tokens);
    }

    public function test_preserves_unicode_characters_in_tokenization()
    {
        // Testing specifically for Finnish letters like ä and ö
        $text = "Hyvää huomenta";
        $tokens = $this->service->tokenize($text);

        $this->assertEquals(['Hyvää', 'huomenta'], $tokens);
    }

    public function test_handles_empty_or_whitespace_strings_in_tokenization()
    {
        $text = "   ";
        $tokens = $this->service->tokenize($text);

        $this->assertIsArray($tokens);
        $this->assertEmpty($tokens);
    }

    public function it_handles_translation_service_failure_and_logs_error()
    {
        // 1. Tell Mockery to expect an error log
        Log::shouldReceive('error')->once();

        // 2. Force the HTTP client to throw a RequestException (Connection failure)
        // This triggers the 'catch' block in your service
        Http::fake([
            '*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException("Connection refused");
            },
        ]);

        $result = $this->service->translate('Error text', 'fi', 'en');

        // 3. Verify it returns null instead of crashing
        $this->assertNull($result);
    }
}
