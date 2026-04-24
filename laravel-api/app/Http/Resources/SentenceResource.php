<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SentenceResource extends JsonResource
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
            'text_target' => $this->text_target,
            'text_source' => $this->text_source,
            // We only include words if they were actually loaded in the controller
            'words' => WordResource::collection($this->whenLoaded('words')),
        ];
    }
}
