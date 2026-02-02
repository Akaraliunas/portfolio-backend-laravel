<?php

namespace App\Http\Controllers\Api;

use App\Models\About;
use Illuminate\Http\JsonResponse;

class AboutController
{
    public function show(): JsonResponse
    {
        $about = About::firstOrFail();

        return response()->json([
            'full_name' => $about->full_name,
            'title' => $about->title,
            'bio' => $about->bio,
            'profile_image' => $about->getProfileImageUrl(),
            'cv_link' => $about->cv_link,
            'social_links' => $about->social_links ?? [],
        ]);
    }
}
