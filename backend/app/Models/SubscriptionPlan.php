<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class SubscriptionPlan extends Model
{
    use HasUuids, SoftDeletes, HasSlug;

    protected $fillable = [
        'name', 'slug', 'description', 'type', 'price_monthly', 'price_yearly',
        'currency', 'max_listings', 'max_photos_per_listing',
        'ai_price_analysis', 'ai_trust_score', 'ai_investment_score',
        'featured_listing', 'priority_support', 'features', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'ai_price_analysis' => 'boolean',
            'ai_trust_score' => 'boolean',
            'ai_investment_score' => 'boolean',
            'featured_listing' => 'boolean',
            'priority_support' => 'boolean',
            'features' => 'array',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }
}
