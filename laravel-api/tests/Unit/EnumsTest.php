<?php

namespace Tests\Unit;

use App\Enums\QuestionType;
use App\Enums\UserRole;
use Tests\TestCase;

class EnumsTest extends TestCase
{
    /**
     * Test QuestionType Enum static values method.
     */
    public function test_question_type_values_returns_all_cases(): void
    {
        $expected = [
            'mcq',
            'scramble',
            'translation',
            'word_meaning',
            'fill_in_the_blank'
        ];

        $this->assertEquals($expected, QuestionType::values());
    }

    /**
     * Test QuestionType getInstructions returns correct string for every case.
     */
    public function test_question_type_instructions_returns_correct_string(): void
    {
        $this->assertEquals('Choose the correct translation from the options below.', QuestionType::MCQ->getInstructions());
        $this->assertEquals('Put the words in the correct order.', QuestionType::SCRAMBLE->getInstructions());
        $this->assertEquals('Type the translation in the target language.', QuestionType::TRANSLATION->getInstructions());
        $this->assertEquals('What is the meaning of this specific word?', QuestionType::WORD_MEANING->getInstructions());
    }

    /**
     * Test that QuestionType throws an error for unhandled match cases.
     * (Useful for identifying if you forgot to add instructions for a new type)
     */
    public function test_question_type_instructions_handles_fill_in_the_blank(): void
    {
        // Note: Your current Enum is missing a match arm for FILL_IN_THE_BLANK.
        // This test will fail until you add it, which is good for coverage!
        $this->expectException(\UnhandledMatchError::class);
        QuestionType::FILL_IN_THE_BLANK->getInstructions();
    }

    /**
     * Test UserRole Enum static values method.
     */
    public function test_user_role_values_returns_all_cases(): void
    {
        $expected = ['Admin', 'Teacher', 'Student'];

        $this->assertEquals($expected, UserRole::values());
    }
}
