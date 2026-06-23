<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CompetitorListing extends Model
{
    use HasUuids;

    protected $fillable = [
        'source_id', 'external_id', 'type', 'title', 'price', 'currency',
        'country', 'city', 'attributes', 'url', 'is_active', 'listed_at',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'is_active' => 'boolean',
            'listed_at' => 'datetime',
        ];
    }

    public function source()
    {
        return $this->belongsTo(CompetitorSource::class, 'source_id');
    }
}
