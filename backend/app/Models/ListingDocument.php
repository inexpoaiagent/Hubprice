<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingDocument extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'listing_id', 'name', 'type', 'file_path', 'file_size', 'mime_type', 'is_verified',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
