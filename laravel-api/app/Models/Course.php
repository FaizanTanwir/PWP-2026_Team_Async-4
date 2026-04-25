<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title The descriptive name of the language course.
 * @property int $source_language_id The ID of the language the student speaks.
 * @property int $target_language_id The ID of the language the student is learning.
 * @property int $created_by_id The ID of the teacher who created the course.
 */
class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'source_language_id',
        'target_language_id',
        'created_by_id',
    ];

    /**
     * The native/instructional language.
     */
    public function sourceLanguage()
    {
        return $this->belongsTo(Language::class, 'source_language_id');
    }

    /**
     * The language being learned.
     */
    public function targetLanguage()
    {
        return $this->belongsTo(Language::class, 'target_language_id');
    }

    /**
     * The teacher who owns/managed this course.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * List of educational modules within this course.
     */
    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
