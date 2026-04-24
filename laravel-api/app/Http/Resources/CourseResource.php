<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'source_language' => new JsonResource($this->whenLoaded('sourceLanguage')),
            'target_language' => new JsonResource($this->whenLoaded('targetLanguage')),
            'teacher' => new UserResource($this->whenLoaded('teacher')),
            'created_at' => $this->created_at,
        ];
    }
}
