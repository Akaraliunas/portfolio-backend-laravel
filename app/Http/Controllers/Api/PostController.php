<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PostController
{
    public function index(): JsonResponse
    {
        $posts = Post::published()
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(fn($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->content,
                'published_at' => $post->published_at?->toIso8601String(),
                'created_at' => $post->created_at->toIso8601String(),
            ]);

        return response()->json([
            'data' => Post::published()->latest()->get()->map(fn($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'description' => Str::limit($post->content, 400),
                'thumbnail' => $post->thumbnail ? asset('storage/' . $post->thumbnail) : null,
                'published_at' => $post->published_at,
            ])
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'published_at' => $post->published_at?->toIso8601String(),
            'created_at' => $post->created_at->toIso8601String(),
            'reading_time' => ceil(str_word_count(strip_tags($post->content)) / 200),
        ]);
    }
}
