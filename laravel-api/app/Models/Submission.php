<?php

namespace App\Models;

use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Define the relationship to the User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship to the Unit.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Attribute to check if the submission was "passed".
     */
    public function getIsPassedAttribute(): bool
    {
        return $this->accuracy >= 0.7;
    }
}
