<?php

namespace App\Http\Resources;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Course
 */
class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'source_language' => new LanguageResource($this->whenLoaded('sourceLanguage')),
            'target_language' => new LanguageResource($this->whenLoaded('targetLanguage')),
            'teacher' => new UserResource($this->whenLoaded('teacher')),

            'units' => UnitResource::collection($this->whenLoaded('units')),

            'created_at' => $this->created_at,
        ];
    }
}
