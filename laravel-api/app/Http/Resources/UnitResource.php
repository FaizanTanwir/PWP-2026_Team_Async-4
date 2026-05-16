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
            'course' => new CourseResource($this->whenLoaded('course')),
            'sentences' => SentenceResource::collection($this->whenLoaded('sentences')),
            'created_at' => $this->created_at?->toDateTimeString(),
    ];
    }
}
