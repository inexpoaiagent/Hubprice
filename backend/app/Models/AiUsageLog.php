<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
class AiUsageLog extends Model { use HasUuids; protected $fillable = ['model_id','purpose','entity_type','entity_id','prompt_tokens','completion_tokens','total_tokens','cost','latency_ms','success','error_message']; }
