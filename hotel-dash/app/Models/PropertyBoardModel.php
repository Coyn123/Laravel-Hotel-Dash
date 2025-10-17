<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyBoardModel extends Model
{
    protected $table = 'property_board';
    protected $fillable = [
        'user_id',
        'property_id',
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
}