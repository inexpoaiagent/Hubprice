<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Payment extends Model { use HasUuids, SoftDeletes; protected $fillable = ['user_id','subscription_id','payment_number','amount','currency','status','gateway','gateway_payment_id','notes']; public function user() { return $this->belongsTo(User::class); } }
