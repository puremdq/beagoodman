<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use App\Http\Model\LikeRecord;

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

    /***
     * 查看传入id用户  是否已经赞了当前文章
     * @param int|string $userId
     * @return boolean
     **/
    public function isUserLiked($userId)
    {
        if (empty($userId)) {

            return false;
        }

        $res = LikeRecord::where([
            ['state', '=', 0],
            ['user_id', '=', intval($userId)],
            ['target_type', '=', '0'],
            ['target_id', '=', $this->dynamic_id],
        ]);

        return $res === null ? false : true;
    }
}
