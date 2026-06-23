<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'business_name', 'business_registration', 'tax_id',
        'bio', 'website', 'city', 'country', 'address',
        'total_listings', 'sold_listings', 'avg_trust_score', 'rating', 'review_count',
        'social_links',
    ];

    protected function casts(): array
    {
        return ['social_links' => 'array'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
