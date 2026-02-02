<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectController
{
    public function index(): JsonResponse
    {
        $projects = Project::all()
            ->map(fn($project) => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'thumbnail' => $project->getThumbnailUrl(),
                'tech_stack' => $project->tech_stack ?? [],
                'github_link' => $project->github_link,
                'live_link' => $project->live_link,
                'order' => $project->order,
            ]);

        return response()->json([
            'data' => $projects,
        ]);
    }
}
