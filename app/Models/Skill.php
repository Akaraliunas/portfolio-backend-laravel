<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Skill extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'category',
        'icon',
        'description',
        'sub_skills',
        'order',
    ];

    protected $casts = [
        'sub_skills' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function ($query) {
            $query->orderBy('category', 'asc')
                ->orderBy('order', 'asc');
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')
            ->singleFile()
            ->useDisk('public');
    }

    public function getIconUrl(): ?string
    {
        return $this->getFirstMediaUrl('icon') ?: null;
    }
}
