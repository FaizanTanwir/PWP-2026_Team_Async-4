<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\Language;
use App\Models\Sentence;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the roles in the test database
        Role::create(['name' => UserRole::TEACHER->value]);
        Role::create(['name' => UserRole::STUDENT->value]);
    }

    /**
     * Test basic attributes and fillable property.
     */
    public function test_word_has_basic_attributes(): void
    {
        $word = Word::create([
            'term' => 'kiitos',
            'lemma' => 'kiitos',
            'translation' => 'thank you',
            'language_id' => Language::factory()->create()->id,
        ]);

        $this->assertEquals('kiitos', $word->term);
        $this->assertEquals('thank you', $word->translation);
    }

    /**
     * Test the BelongsTo relationship with Language.
     * This is likely the missing 50% of your coverage.
     */
    public function test_word_belongs_to_a_language(): void
    {
        $language = Language::factory()->create(['name' => 'Finnish']);
        $word = Word::factory()->create(['language_id' => $language->id]);

        $this->assertInstanceOf(Language::class, $word->language);
        $this->assertEquals($language->id, $word->language->id);
        $this->assertEquals('Finnish', $word->language->name);
    }

    public function test_word_belongs_to_many_sentences(): void
    {
        $word = Word::factory()->create();
        $sentences = Sentence::factory()->count(2)->create();

        // Attach sentences to the word via pivot table
        $word->sentences()->attach($sentences->pluck('id'));

        $this->assertCount(2, $word->sentences);
        $this->assertInstanceOf(Sentence::class, $word->sentences->first());

        // Verify pivot table persistence
        $this->assertDatabaseHas('sentence_word', [
            'word_id' => $word->id,
            'sentence_id' => $sentences->first()->id,
        ]);
    }
}
