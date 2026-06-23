<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiModel extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name', 'provider', 'model_id', 'purpose', 'is_active', 'is_default',
        'temperature', 'max_tokens', 'top_p', 'cost_per_1k_tokens',
        'monthly_cost_limit', 'rate_limit_per_minute', 'fallback_chain', 'config',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'fallback_chain' => 'array',
            'config' => 'array',
        ];
    }
}
