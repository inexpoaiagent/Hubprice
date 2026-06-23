<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rental extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'title', 'rental_type', 'bedrooms', 'bathrooms', 'area_sqm',
        'furnishing', 'amenities', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'meta' => 'array',
        ];
    }

    public function listing()
    {
        return $this->morphOne(Listing::class, 'listable');
    }
}
