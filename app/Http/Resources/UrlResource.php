<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UrlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'original_url' => $this->original_url,
            'short_url' => $this->short_url,
            'visit_count' => $this->visit_count,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
