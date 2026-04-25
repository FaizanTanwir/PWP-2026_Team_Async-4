<?php

namespace App\Enums;

/**
 * The supported formats for educational exercises.
 */
enum QuestionType: string
{
    /** Multiple choice question with one correct answer. */
    case MCQ = 'mcq';

    /** Rearrange jumbled words into a coherent sentence. */
    case SCRAMBLE = 'scramble';

    /** Direct text translation from source to target language. */
    case TRANSLATION = 'translation';

    /** Vocabulary focus on the meaning of a specific term. */
    case WORD_MEANING = 'word_meaning';

    /** Contextual exercise where the user provides a missing word. */
    case FILL_IN_THE_BLANK = 'fill_in_the_blank';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getInstructions(): string
    {
        return match($this) {
            self::MCQ => 'Choose the correct translation from the options below.',
            self::SCRAMBLE => 'Put the words in the correct order.',
            self::TRANSLATION => 'Type the translation in the target language.',
            self::WORD_MEANING => 'What is the meaning of this specific word?',
            self::FILL_IN_THE_BLANK => 'Complete the sentence by filling in the missing word.',
        };
    }
}
