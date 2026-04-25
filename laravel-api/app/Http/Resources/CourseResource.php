<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Course
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
            // We use simple JsonResource for languages or create a LanguageResource
            'source_language' => new JsonResource($this->whenLoaded('sourceLanguage')),
            'target_language' => new JsonResource($this->whenLoaded('targetLanguage')),
            'teacher' => new UserResource($this->whenLoaded('teacher')),

            // Nested units: Only shown if loaded (e.g., in show() method)
            'units' => UnitResource::collection($this->whenLoaded('units')),

            'created_at' => $this->created_at,
        ];
    }
}
