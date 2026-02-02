<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class About extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'full_name',
        'title',
        'bio',
        'social_links',
        'cv_link',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image')
            ->singleFile()
            ->useDisk('public');
    }

    public function getProfileImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('profile_image') ?: null;
    }
}
