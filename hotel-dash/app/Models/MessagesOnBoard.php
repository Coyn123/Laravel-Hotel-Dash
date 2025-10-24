<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MessagesOnBoard extends Model
{
    protected $table = 'messages_on_board';

    protected $fillable = [
        'user_id',
        'property_id',
        'floor_id',
        'room_id',
        'flag_id',
        'message_text',
        'board_type',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flag()
    {
        return $this->belongsTo(MessageFlag::class, 'flag_id');
    }

    /*
     |--------------------------------------------------------------------------
     | Query Scopes
     |--------------------------------------------------------------------------
     */
    public function scopeRoomBoard(Builder $query)
    {
        return $query->where('board_type', 'room_board');
    }

    public function scopePropertyBoard(Builder $query)
    {
        return $query->where('board_type', 'property_board');
    }

    /*
     |--------------------------------------------------------------------------
     | Factory Helpers
     |--------------------------------------------------------------------------
     */
    public static function createRoomBoard(array $attributes)
    {
        $attributes['board_type'] = 'room_board';
        return static::create($attributes);
    }

    public static function createPropertyBoard(array $attributes)
    {
        $attributes['board_type'] = 'property_board';
        return static::create($attributes);
    }
}
