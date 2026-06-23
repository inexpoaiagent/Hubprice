<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class VehicleModel extends Model
{
    use HasUuids, SoftDeletes, HasSlug;

    protected $table = 'vehicle_models';

    protected $fillable = ['brand_id', 'name', 'slug', 'body_type', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug')->allowDuplicateSlugs();
    }

    public function brand()
    {
        return $this->belongsTo(VehicleBrand::class);
    }
}
