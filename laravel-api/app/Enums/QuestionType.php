<?php

namespace App\Enums;

enum QuestionType: string
{
    case MCQ = 'mcq';
    case SCRAMBLE = 'scramble';
    case TRANSLATION = 'translation';
    case WORD_MEANING = 'word_meaning';
    case FILL_IN_THE_BLANK = 'fill_in_the_blank';

    // Get all keys for validation or seeding
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // Helper to provide default instructions for the UI
    public function getInstructions(): string
    {
        return match($this) {
            self::MCQ => 'Choose the correct translation from the options below.',
            self::SCRAMBLE => 'Put the words in the correct order.',
            self::TRANSLATION => 'Type the translation in the target language.',
            self::WORD_MEANING => 'What is the meaning of this specific word?',
        };
    }
}
