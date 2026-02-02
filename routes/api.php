<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    // Public portfolio endpoints
    Route::get('/about', [AboutController::class, 'show']);
    Route::get('/experience', [ExperienceController::class, 'index']);
    Route::get('/skills', [SkillController::class, 'index']);
    Route::get('/projects', [ProjectController::class, 'index']);
    
    // Blog endpoints
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{slug}', [PostController::class, 'show']);
    
    // Contact form (rate limited)
    Route::post('/contact', [ContactController::class, 'store']);
});
