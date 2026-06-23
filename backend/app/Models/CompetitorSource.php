<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetitorSource extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name', 'url', 'type', 'is_active', 'scrape_interval', 'last_scraped_at', 'config',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_scraped_at' => 'datetime',
            'config' => 'array',
        ];
    }

    public function listings()
    {
        return $this->hasMany(CompetitorListing::class, 'source_id');
    }
}
