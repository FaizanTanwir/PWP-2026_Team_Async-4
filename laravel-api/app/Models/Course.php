<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function sourceLanguage()
    {
        return $this->belongsTo(Language::class, 'source_language_id');
    }
    public function targetLanguage()
    {
        return $this->belongsTo(Language::class, 'target_language_id');
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
