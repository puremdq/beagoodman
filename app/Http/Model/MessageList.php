<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class MessageList extends Model
{
    protected $table = 'message_list';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    public $timestamps = false;
}
