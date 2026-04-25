<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
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
