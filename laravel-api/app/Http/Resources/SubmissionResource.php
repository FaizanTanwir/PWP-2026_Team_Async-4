<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Submission
 */
class SubmissionResource extends JsonResource
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
            'type' => $this->type,
            'question_text' => $this->question_text,
            'provided_answer' => $this->provided_answer,
            'correct_answer' => $this->correct_answer,
            'accuracy' => $this->accuracy,
            'is_passed' => $this->is_passed,
            'submitted_at' => $this->created_at?->toDateTimeString(),

            // Nested data for detailed views
            'user' => new UserResource($this->whenLoaded('user')),
            'unit' => new JsonResource($this->whenLoaded('unit')),
        ];
    }
}
