<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FeatureAnnouncement extends Model
{
    protected $fillable = [
        'title',
        'type',
        'version',
        'body',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function reads(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'feature_announcement_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }
}
