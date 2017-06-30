<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{


    protected $table = 'notification';
    protected $primaryKey = 'notification_id';

    protected $guarded = ['notification_id'];
    public $timestamps = false;


    public function fromUser()
    {

        return $this->hasOne('App\Http\Model\User', 'user_id', 'from_user_id');

    }

//0 赞了 你 动态     1 评论了你的动态  2赞了你的评论  3回复了你的评论  4@了你   5关注了你 6站内信
    public function target()
    {
        $type = intval($this->notification_type);

        if ($type == 1 || $type == 3 || $type == 4) {

            return $this->hasOne('App\Http\Model\Comment', 'comment_id', 'target_id');

        } else if ($type == 0 || $type == 2) {
            return $this->hasOne('App\Http\Model\LikeRecord', 'id', 'target_id');


        } else if ($type == 5) {
            return $this->hasOne('App\Http\Model\UserRelation', 'relation_id', 'target_id');


        } else if ($type == 6) {
            return $this->hasOne('App\Http\Model\Message', 'message_id', 'target_id');


        } else {

            return null;
        }

    }


}
