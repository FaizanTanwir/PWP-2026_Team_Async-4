<?php

namespace App\Models;

use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id The ID of the student making the submission.
 * @property int $unit_id The ID of the unit being practiced.
 * @property QuestionType $type The exercise format used.
 * @property string $question_text The prompt shown to the user.
 * @property string $provided_answer The answer typed or selected by the student.
 * @property string $correct_answer The actual correct answer for comparison.
 * @property float $accuracy The score ranging from 0.0 (0%) to 1.0 (100%).
 * @property-read bool $is_passed Returns true if accuracy is 70% or higher.
 */
class Submission extends Model
{
    /** @use HasFactory<\Database\Factories\SubmissionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'unit_id',
        'type',
        'question_text',
        'provided_answer',
        'correct_answer',
        'accuracy',
    ];

    protected $casts = [
        'type' => QuestionType::class,
        'accuracy' => 'float',
    ];

    /**
     * The student who submitted this exercise.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The unit associated with this practice session.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Computed attribute to determine passing status.
     * * A submission is considered passed if the accuracy is >= 0.7.
     */
    public function getIsPassedAttribute(): bool
    {
        return $this->accuracy >= 0.7;
    }
}
