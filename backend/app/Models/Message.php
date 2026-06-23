<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Message extends Model { use HasUuids, SoftDeletes; protected $fillable = ['conversation_id','sender_id','body','attachments','read_at']; protected function casts(): array { return ['attachments' => 'array', 'read_at' => 'datetime']; } public function sender() { return $this->belongsTo(User::class, 'sender_id'); } }
