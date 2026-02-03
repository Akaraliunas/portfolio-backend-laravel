<?php

namespace App\Http\Controllers\Api;

use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\SkillResource;

class SkillController
{
    public function index(): JsonResponse
    {
        $skills = Skill::query()
            ->orderBy('order')
            ->get(); // Remove ->groupBy('category')

        return response()->json([
            // Now it returns a simple flat array
            'data' => SkillResource::collection($skills),
        ]);
    }

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
