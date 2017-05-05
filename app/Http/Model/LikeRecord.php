<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class LikeRecord extends Model
{

    protected $table = 'like_record';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    public $timestamps = false;


    public function target()
    {

        if ($this->target_type == 0) {

            return $this->hasOne('App\Http\Model\Dynamic', 'dynamic_id', 'target_id');

        } else if ($this->target_type == 1) {

            return $this->hasOne('App\Http\Model\Comment', 'comment_id', 'target_id');

        } else {

            return false;
        }
    }


}
