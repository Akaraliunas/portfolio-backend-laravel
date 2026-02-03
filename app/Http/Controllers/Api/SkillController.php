<?php

namespace App\Http\Controllers\Api;

use App\Models\Skill;
use Illuminate\Http\JsonResponse;

class SkillController
{
    public function index(): JsonResponse
    {
        $skills = Skill::query()
            ->orderBy('order')
            ->get()
            ->groupBy('category');

        // We wrap the grouped collection with our Resource
        return response()->json([
            'data' => $skills->map(fn($group) => SkillResource::collection($group)),
        ]);
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'icon' => $this->icon,
            'icon_url' => $this->getIconUrl(),
            'description' => $this->description,
            'sub_skills' => $this->sub_skills ?? [],
            'order' => $this->order,
        ];
    }
}
