<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Unit
 */
class UnitResource extends JsonResource
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

            // Nested Course details - only if requested/loaded
            'course' => new CourseResource($this->whenLoaded('course')),

            // Nested Sentences - crucial for the learning UI
            'sentences' => SentenceResource::collection($this->whenLoaded('sentences')),

            'created_at' => $this->created_at?->toDateTimeString(),
    ];
    }
}
