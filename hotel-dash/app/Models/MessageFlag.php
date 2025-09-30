<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageFlag extends Model
{
    protected $table = 'message_flags';
    public $timestamps = false;

    protected $fillable = ['flag_name'];

    public function messages()
    {
        return $this->hasMany(MessageBoard::class, 'flag_id');
    }
}
