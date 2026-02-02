<?php

namespace App\Http\Controllers\Api;

use App\Models\Skill;
use Illuminate\Http\JsonResponse;

class SkillController
{
    public function index(): JsonResponse
    {
        $skills = Skill::all()
            ->groupBy('category')
            ->map(fn($group) => $group->map(fn($skill) => [
                'id' => $skill->id,
                'category' => $skill->category,
                'icon' => $skill->icon,
                'icon_url' => $skill->getIconUrl(),
                'description' => $skill->description,
                'sub_skills' => $skill->sub_skills ?? [],
                'order' => $skill->order,
            ])->toArray())
            ->toArray();

        return response()->json([
            'data' => $skills,
        ]);
    }
}
