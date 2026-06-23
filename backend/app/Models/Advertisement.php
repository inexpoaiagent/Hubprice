<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertisement extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name', 'slot', 'type', 'content', 'image', 'link',
        'is_active', 'starts_at', 'ends_at',
        'impression_count', 'click_count', 'listing_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
