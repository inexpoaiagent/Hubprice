<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FraudReport extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'listing_id', 'reported_by', 'reason', 'description',
        'risk_score', 'ai_analysis', 'status',
    ];

    protected function casts(): array
    {
        return ['ai_analysis' => 'array'];
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
