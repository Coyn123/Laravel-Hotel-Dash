<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomBoardModel extends Model
{
    protected $table = 'room_board';
    protected $fillable = [
        'user_id',
        'property_id',
        'floor_id',
        'room_id',
        'flag_id',
        'message_text',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relationships
    public function flag()
    {
        return $this->belongsTo(MessageFlag::class, 'flag_id');
    }
    /*
    public function property()
    {
        return $this->belongsTo(PropertiesConfig::class, 'property_id');
    }

    public function floor()
    {
        return $this->belongsTo(FloorsConfig::class, 'floor_id');
    }

    public function room()
    {
        return $this->belongsTo(RoomsConfig::class, 'room_id');
    }
        */
}
