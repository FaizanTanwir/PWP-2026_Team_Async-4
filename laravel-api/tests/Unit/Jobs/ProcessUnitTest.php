<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessUnitFile;
use App\Models\Course;
use App\Models\Language;
use App\Models\Unit;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery\MockInterface;

class ProcessUnitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    public function test_processes_file_content_and_creates_sentences_and_words(): void
    {
        // 1. Setup specific languages to match the translator calls
        $sourceLang = Language::factory()->create(['code' => 'en']);
        $targetLang = Language::factory()->create(['code' => 'fi']);

        $user = User::factory()->create();
        $course = Course::factory()->create([
            'source_language_id' => $sourceLang->id,
            'target_language_id' => $targetLang->id
        ]);
        $unit = Unit::factory()->create(['course_id' => $course->id]);
        $content = "Tervetuloa Ouluun";

        // 2. Mock the translator with strict argument matching
        $this->mock(TranslationService::class, function (MockInterface $mock) {
            // Expect: Target Text, From: FI, To: EN
            $mock->shouldReceive('translate')
                ->once()
                ->with('Tervetuloa Ouluun', 'fi', 'en')
                ->andReturn('Welcome to Oulu');

            $mock->shouldReceive('tokenize')
                ->once()
                ->with('Tervetuloa Ouluun')
                ->andReturn(['Tervetuloa', 'Ouluun']);

            // Word translations also need to be flipped
            $mock->shouldReceive('translate')
                ->with('Tervetuloa', 'fi', 'en')
                ->andReturn('Welcome');

            $mock->shouldReceive('translate')
                ->with('Ouluun', 'fi', 'en')
                ->andReturn('to Oulu');
        });

        // 3. Run the job manually
        $job = new ProcessUnitFile($unit, $user, $content);
        app()->call([$job, 'handle']);

        // 4. Assertions
        $this->assertDatabaseHas('sentences', [
            'unit_id' => $unit->id,
            'text_target' => 'Tervetuloa Ouluun',
            'text_source' => 'Welcome to Oulu' // This will now pass!
        ]);

        $this->assertDatabaseHas('words', ['term' => 'Tervetuloa', 'translation' => 'Welcome']);
        $this->assertDatabaseHas('words', ['term' => 'Ouluun', 'translation' => 'to Oulu']);

        $this->assertEquals(2, $unit->sentences()->first()->words()->count());
    }
}
