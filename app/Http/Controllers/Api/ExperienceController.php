<?php

namespace App\Http\Controllers\Api;

use App\Models\Experience;
use Illuminate\Http\JsonResponse;

class ExperienceController
{
    public function index(): JsonResponse
    {
        $experiences = Experience::all()
            ->map(fn($exp) => [
                'id' => $exp->id,
                'company_name' => $exp->company_name,
                'role' => $exp->role,
                'period' => $exp->period,
                'description' => $exp->description,
                'technologies' => $exp->technologies ?? [],
                'order' => $exp->order,
            ]);

        return response()->json([
            'data' => $experiences,
        ]);
    }
}
