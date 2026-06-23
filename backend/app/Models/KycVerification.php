<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KycVerification extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id', 'document_type', 'document_number', 'full_name',
        'date_of_birth', 'nationality', 'document_front', 'document_back',
        'selfie', 'status', 'rejection_reason', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
