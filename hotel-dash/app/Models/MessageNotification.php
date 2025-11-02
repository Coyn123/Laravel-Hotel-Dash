<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageNotification extends Model
{
    protected $table = 'message_notifications';
    protected $fillable = ['message_id', 'user_id', 'read_at', 'flag_id'];
    public $timestamps = false;

    public function message()
    {
        // Assuming your unified table model is MessagesOnBoard
        return $this->belongsTo(MessagesOnBoard::class, 'message_id');
    }
}
