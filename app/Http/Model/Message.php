<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use App\Http\Model\User;

class Message extends Model
{

    protected $table = 'message';
    protected $primaryKey = 'message_id';
    protected $guarded = ['message_id'];
    public $timestamps = false;

    public function fromUserAvatar(){

        return User::getAvatar($this->from_id);



    }

}
