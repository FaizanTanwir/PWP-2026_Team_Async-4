<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Jobs\ProcessUnitFile;
use App\Models\Course;
use App\Models\Language;
use App\Models\Sentence;
use App\Models\Unit;
use App\Models\User;
use App\Models\Word;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class SentenceFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected Language $language;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->language = Language::factory()->create([
            'code' => 'fi'
        ]);
    }

    private function createUser(UserRole $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role->value);
        return $user;
    }

    // --- ACCESS CONTROL (All protected by Sanctum) ---

    public function test_guest_cannot_access_any_sentence_route()
    {
        $unit = Unit::factory()->create();
        $this->getJson("/api/units/{$unit->id}/sentences")->assertStatus(401);
    }

    public function test_authenticated_user_can_list_unit_sentences()
    {
        $user = $this->createUser(UserRole::STUDENT);
        $unit = Unit::factory()->create();

        Sentence::factory()->count(3)->create(['unit_id' => $unit->id, 'user_id' => $user->id]);

        $this->actingAs($user)
            ->getJson("/api/units/{$unit->id}/sentences")
            ->assertStatus(200)
            ->assertJsonCount(3);
    }

    // --- CREATION LOGIC ---

    public function test_teacher_can_create_sentence_in_their_own_course_unit()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $course = Course::factory()->create(['created_by_id' => $teacher->id]);
        $unit = Unit::factory()->create(['course_id' => $course->id]);

        $payload = [
            'text_target' => 'Minä asun Oulussa',
            'text_source' => 'I live in Oulu',
            'words' => [
                ['term' => 'Minä', 'translation' => 'I', 'lemma' => 'minä'],
            ]
        ];

        $this->actingAs($teacher)
            ->postJson("/api/units/{$unit->id}/sentences", $payload)
            ->assertStatus(201);
    }

    public function test_teacher_cannot_create_sentence_in_another_teachers_unit()
    {
        $teacherA = $this->createUser(UserRole::TEACHER);
        $teacherB = $this->createUser(UserRole::TEACHER);

        $courseA = Course::factory()->create(['created_by_id' => $teacherA->id]);
        $unitA = Unit::factory()->create(['course_id' => $courseA->id]);

        $this->actingAs($teacherB)
        ->postJson("/api/units/{$unitA->id}/sentences", [
            'text_target' => 'Unauthorized',
            'text_source' => 'Unauthorized',
            'words' => [['term' => 'Hack', 'translation' => 'Hack', 'lemma' => 'hack']]
        ])
        ->assertStatus(403);
    }

    public function test_store_fails_if_words_array_is_empty()
    {
        $admin = $this->createUser(UserRole::ADMIN);

        $course = Course::factory()->create(['created_by_id' => $admin->id]);
        $unit = Unit::factory()->create(['course_id' => $course->id]);

        $this->actingAs($admin)
            ->postJson("/api/units/{$unit->id}/sentences", [
                'text_target' => 'Test',
                'text_source' => 'Test',
                'words' => []
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['words']);
    }

    public function test_teacher_can_create_sentence_with_words()
    {
        $this->withoutExceptionHandling();
        $teacher = $this->createUser(UserRole::TEACHER);
        $course = Course::factory()->create(['created_by_id' => $teacher->id]);
        $unit = Unit::factory()->create(['course_id' => $course->id]);

        $payload = [
            'text_target' => 'Minä rakastan Oulua',
            'text_source' => 'I love Oulu',
            'unit_id' => $unit->id,
            'words' => [
                ['term' => 'Minä', 'translation' => 'I', 'lemma' => 'minä'],
                ['term' => 'rakastan', 'translation' => 'love', 'lemma' => 'rakastaa']
            ]
        ];

        $response = $this->actingAs($teacher)
            ->postJson("/api/units/{$unit->id}/sentences", $payload);

        $response->assertStatus(201)
            ->assertJsonPath('text_target', 'Minä rakastan Oulua')
            ->assertJsonCount(2, 'words');

        $this->assertDatabaseHas('sentences', ['text_target' => 'Minä rakastan Oulua']);
        $this->assertDatabaseHas('words', ['term' => 'rakastan', 'translation' => 'love']);
    }

    // --- UPDATE & DELETE (The Short-Path Checks) ---

    public function test_teacher_can_update_their_own_sentence()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $sentence = Sentence::factory()->create(['user_id' => $teacher->id]);

        $this->actingAs($teacher)
            ->patchJson("/api/sentences/{$sentence->id}", [
                'text_target' => 'Updated Text'
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('sentences', ['id' => $sentence->id, 'text_target' => 'Updated Text']);
    }

    public function test_teacher_cannot_update_another_teachers_sentence()
    {
        $teacherA = $this->createUser(UserRole::TEACHER);
        $teacherB = $this->createUser(UserRole::TEACHER);
        $sentenceA = Sentence::factory()->create(['user_id' => $teacherA->id]);

        $this->actingAs($teacherB)
            ->patchJson("/api/sentences/{$sentenceA->id}", [
                'text_target' => 'Hacker edit'
            ])
            ->assertStatus(403);
    }

    public function test_admin_can_update_any_sentence()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $sentence = Sentence::factory()->create(); // Created by someone else

        $this->actingAs($admin)
            ->patchJson("/api/sentences/{$sentence->id}", ['text_target' => 'Admin fix'])
            ->assertStatus(200);
    }

    public function test_teacher_can_delete_their_own_sentence()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $sentence = Sentence::factory()->create(['user_id' => $teacher->id]);

        $this->actingAs($teacher)
            ->deleteJson("/api/sentences/{$sentence->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('sentences', ['id' => $sentence->id]);
    }

    public function test_teacher_cannot_delete_others_sentence()
    {
        $teacherA = $this->createUser(UserRole::TEACHER);
        $teacherB = $this->createUser(UserRole::TEACHER);
        $sentenceA = Sentence::factory()->create(['user_id' => $teacherA->id]);

        $this->actingAs($teacherB)
            ->deleteJson("/api/sentences/{$sentenceA->id}")
            ->assertStatus(403);
    }

    // --- EDGE CASE: VALIDATION ---

    public function test_sentence_creation_fails_without_required_fields()
    {
        $admin = $this->createUser(UserRole::ADMIN);

        $course = Course::factory()->create(['created_by_id' => $admin->id]);
        $unit = Unit::factory()->create(['course_id' => $course->id]);

        $this->actingAs($admin)
            ->postJson("/api/units/{$unit->id}/sentences", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['text_target', 'text_source']);
    }

    /** --- UPDATE & SYNC TESTS --- **/

    public function test_update_syncs_words_correctly_removes_old_ones()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $sentence = Sentence::factory()->create(['user_id' => $teacher->id]);

        // Attach initial words
        $word1 = Word::factory()->create(['term' => 'KeepMe', 'language_id' => $this->language->id]);
        $word2 = Word::factory()->create(['term' => 'RemoveMe', 'language_id' => $this->language->id]);
        $sentence->words()->attach([$word1->id, $word2->id]);

        $payload = [
            'words' => [
                ['term' => 'KeepMe', 'translation' => 'Staying', 'language_id' => $this->language->id],
                ['term' => 'NewWord', 'translation' => 'Added', 'language_id' => $this->language->id]
            ]
        ];

        $this->actingAs($teacher)
            ->patchJson("/api/sentences/{$sentence->id}", $payload)
            ->assertStatus(200);

        // Verify word2 is detached but word1 and NewWord are present
        $this->assertEquals(2, $sentence->words()->count());
        $this->assertFalse($sentence->words()->where('term', 'RemoveMe')->exists());
        $this->assertTrue($sentence->words()->where('term', 'NewWord')->exists());
    }

    public function test_update_or_create_updates_global_word_translation()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $word = Word::factory()->create(['term' => 'Kissa', 'translation' => 'Dog']); // Incorrect
        $sentence = Sentence::factory()->create();

        $payload = [
            'words' => [
                ['term' => 'Kissa', 'translation' => 'Cat'] // Correction
            ]
        ];

        $this->actingAs($admin)
            ->patchJson("/api/sentences/{$sentence->id}", $payload)
            ->assertStatus(200);

        // Verify the global word was corrected in the database
        $this->assertDatabaseHas('words', [
            'term' => 'Kissa',
            'translation' => 'Cat'
        ]);
    }

    /**
     * Test the automated translation and tokenization preview.
     */
    public function test_teacher_can_preview_translations()
    {
        // 1. Setup Course with specific language codes
        $teacher = $this->createUser(UserRole::TEACHER);
        $sourceLang = Language::factory()->create(['code' => 'en']);
        $course = Course::factory()->create([
            'created_by_id' => $teacher->id,
            'target_language_id' => $this->language->id, // fi
            'source_language_id' => $sourceLang->id,     // en
        ]);
        $unit = Unit::factory()->create(['course_id' => $course->id]);

        // 2. Fake the LibreTranslate API response
        Http::fake([
            '*' => Http::response(['translatedText' => 'translated result'], 200),
        ]);

        $payload = ['text' => 'Kissa istuu'];

        // 3. Execute request
        $response = $this->actingAs($teacher)
            ->postJson("/api/units/{$unit->id}/sentences/preview", $payload);

        // 4. Assert structure and data
        $response->assertStatus(200)
            ->assertJsonStructure([
                'sentence' => ['text_target', 'text_source'],
                'words' => [
                    '*' => ['term', 'translation', 'lemma']
                ],
                'meta' => ['target_lang', 'source_lang']
            ])
            ->assertJsonPath('sentence.text_target', 'Kissa istuu')
            ->assertJsonPath('meta.target_lang', 'fi');
    }

    /**
     * Test that preview requires the text field.
     */
    public function test_preview_fails_without_text()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $unit = Unit::factory()->create();

        $this->actingAs($teacher)
            ->postJson("/api/units/{$unit->id}/sentences/preview", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['text']);
    }

    public function test_teacher_can_upload_txt_file_for_bulk_processing()
    {
        Queue::fake();
        $teacher = $this->createUser(UserRole::TEACHER);
        $course = Course::factory()->create(['created_by_id' => $teacher->id]);
        $unit = Unit::factory()->create(['course_id' => $course->id]);

        // Create a dummy text file
        $content = "First sentence\nSecond sentence";
        $file = UploadedFile::fake()->createWithContent('lesson.txt', $content);

        $response = $this->actingAs($teacher)
            ->postJson("/api/units/{$unit->id}/sentences/upload", [
                'file' => $file
            ]);

        $response->assertStatus(202);

        // Assert the job was pushed to the queue with correct data
        Queue::assertPushed(ProcessUnitFile::class, function ($job) use ($unit, $teacher) {
            return $job->unit->id === $unit->id && $job->user->id === $teacher->id;
        });
    }
}
