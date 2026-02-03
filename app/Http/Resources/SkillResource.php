<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'icon' => $this->icon,
            'description' => $this->description,
            'sub_skills' => $this->sub_skills ?? [],
            'order' => $this->order,
        ];
    }
}
