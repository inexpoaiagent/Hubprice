<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Conversation extends Model { use HasUuids, SoftDeletes; protected $fillable = ['listing_id','buyer_id','seller_id','subject','last_message_at','buyer_archived','seller_archived']; protected function casts(): array { return ['last_message_at' => 'datetime', 'buyer_archived' => 'boolean', 'seller_archived' => 'boolean']; } public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); } public function seller() { return $this->belongsTo(User::class, 'seller_id'); } public function listing() { return $this->belongsTo(Listing::class); } public function messages() { return $this->hasMany(Message::class); } }
