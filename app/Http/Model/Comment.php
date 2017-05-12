<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    protected $table = 'comment';
    protected $primaryKey = 'comment_id';
    protected $guarded = ['comment_id'];
    public $timestamps = false;


    public function author()
    {


        return $this->hasOne('App\Http\Model\User', 'user_id', 'user_id');

    }

    public function target()
    {

        return $this->belongsTo('App\Http\Model\Dynamic', 'comment_target', 'dynamic_id');
    }

    public function parentComment()
    {

        if ($this->comment_pid != 0) {

            return self::find($this->comment_pid);
        } else {

            return null;
        }
    }


}
