<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Project extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'tech_stack',
        'github_link',
        'live_link',
        'order',
    ];

    protected $casts = [
        'tech_stack' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function ($query) {
            $query->orderBy('order', 'asc');
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->useDisk('public');
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->getFirstMediaUrl('thumbnail') ?: null;
    }
}
