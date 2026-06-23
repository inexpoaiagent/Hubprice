<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    use HasUuids;

    protected $fillable = ['listing_id', 'price', 'currency', 'changed_by', 'reason'];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
