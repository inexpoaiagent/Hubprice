<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ListingView extends Model
{
    use HasUuids;

    protected $fillable = ['listing_id', 'user_id', 'ip_address', 'user_agent'];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
