<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Dynamic extends Model
{


    protected $table = 'dynamic';
    protected $primaryKey = 'dynamic_id';

    protected $guarded = ['dynamic_id'];
    public $timestamps = false;


    public function target()
    {

        if ($this->dynamic_type == 1) {

            return $this->hasOne('App\Http\Model\Article', 'article_id', 'target_id');

        } else {

            return $this->hasOne('App\Http\Model\Mood', 'mood_id', 'target_id');

        }
    }

    public function author()
    {

        return $this->hasOne('App\Http\Model\User', 'user_id', 'user_id');

    }


    public function comments(){

        return $this->hasMany('App\Http\Model\Comment','comment_target','dynamic_id');


    }

}
