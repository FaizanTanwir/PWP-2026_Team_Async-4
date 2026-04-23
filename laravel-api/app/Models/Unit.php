<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    /** @use HasFactory<\Database\Factories\UnitFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'course_id',
    ];

    /**
     * Get the course that owns the unit.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function sentences(): HasMany
    {
        return $this->hasMany(Sentence::class);
    }
}
