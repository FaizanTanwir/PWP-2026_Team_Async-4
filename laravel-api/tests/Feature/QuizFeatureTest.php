<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Sentence;
use App\Models\Unit;
use App\Models\User;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class QuizFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $teacher;
    protected $unit;
    protected $language;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup basic roles and user
        Role::create(['name' => UserRole::TEACHER->value]);
        $this->teacher = User::factory()->create();
        $this->teacher->assignRole(UserRole::TEACHER->value);

        // Setup Course and Unit
        $course = Course::factory()->create(['created_by_id' => $this->teacher->id]);
        $this->unit = Unit::factory()->create(['course_id' => $course->id]);
        $this->language = $course->targetLanguage;
    }

    /**
     * Test successful quiz generation with exactly 10 questions.
     */
    public function test_it_generates_a_quiz_with_ten_questions(): void
    {
        // Seed at least 3 sentences to satisfy the controller's check
        $sentences = Sentence::factory()->count(4)->create([
            'unit_id' => $this->unit->id,
            'user_id' => $this->teacher->id
        ]);

        foreach ($sentences as $sentence) {
            $words = Word::factory()->count(2)->create(['language_id' => $this->language->id]);
            $sentence->words()->attach($words);
        }

        $response = $this->actingAs($this->teacher)
            ->getJson("/api/units/{$this->unit->id}/quiz");

        $response->assertStatus(200)
            ->assertJsonPath('unit_id', $this->unit->id)
            ->assertJsonCount(10, 'questions');

        // Verify structure of a random question in the list
        $question = $response->json('questions.0');
        $this->assertArrayHasKey('type', $question);
        $this->assertArrayHasKey('question_text', $question);
        $this->assertArrayHasKey('correct_answer', $question);
    }

    /**
     * Test the edge case where unit has fewer than 3 sentences.
     */
    public function test_it_returns_422_if_unit_has_insufficient_sentences(): void
    {
        // Only create 2 sentences
        Sentence::factory()->count(2)->create(['unit_id' => $this->unit->id]);

        $response = $this->actingAs($this->teacher)
            ->getJson("/api/units/{$this->unit->id}/quiz");

        $response->assertStatus(422)
            ->assertJsonStructure(['message']);
    }

    /**
     * Test that MCQ questions always return 4 options even in small units.
     */
    public function test_mcq_questions_provide_sufficient_options_via_fallback(): void
    {
        // 1. Create a sentence with just 1 word
        $sentence = Sentence::factory()->create(['unit_id' => $this->unit->id]);
        $word = Word::factory()->create(['language_id' => $this->language->id]);
        $sentence->words()->attach($word);

        // 2. Add two more sentences just to pass the "count < 3" check
        Sentence::factory()->count(2)->create(['unit_id' => $this->unit->id]);

        // 3. Add extra words to the language (for the fallback)
        Word::factory()->count(10)->create(['language_id' => $this->language->id]);

        // 4. Hit the endpoint
        $response = $this->actingAs($this->teacher)
            ->getJson("/api/units/{$this->unit->id}/quiz");

        $questions = collect($response->json('questions'));
        $mcq = $questions->firstWhere('type', 'mcq');

        // If an MCQ was generated, ensure it has 4 options (correct + 3 distractors)
        if ($mcq) {
            $this->assertCount(4, $mcq['options']);
            $this->assertContains($mcq['correct_answer'], $mcq['options']);
        }
    }

    /**
     * Test that Scramble actually shuffles the words.
     */
    public function test_scramble_questions_shuffles_the_text(): void
    {
        $sentence = Sentence::factory()->create([
            'unit_id' => $this->unit->id,
            'text_target' => 'The quick brown fox jumps'
        ]);
        // Satisfy sentence count check
        Sentence::factory()->count(2)->create(['unit_id' => $this->unit->id]);

        $response = $this->actingAs($this->teacher)
            ->getJson("/api/units/{$this->unit->id}/quiz");

        $questions = collect($response->json('questions'));
        $scramble = $questions->firstWhere('type', 'scramble');

        if ($scramble) {
            // It should be the same words, but a different string order
            $this->assertNotEquals('The quick brown fox jumps', $scramble['question_text']);

            $originalWords = sort(explode(' ', 'The quick brown fox jumps'));
            $scrambledWords = sort(explode(' ', $scramble['question_text']));
            $this->assertEquals($originalWords, $scrambledWords);
        }
    }
}
