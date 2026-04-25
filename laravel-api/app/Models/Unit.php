<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title The name of the learning module (e.g., "Basic Greetings").
 * @property int $course_id The ID of the course this unit belongs to.
 */
class Unit extends Model
{
    /** @use HasFactory<\Database\Factories\UnitFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'course_id',
    ];

    /**
     * The parent course containing this unit.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * The practice sentences associated with this module.
     */
    public function sentences(): HasMany
    {
        return $this->hasMany(Sentence::class);
    }
}
