<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class User extends Model
{


    protected $table = 'user';
    protected $primaryKey = 'user_id';
    protected $guarded = ['user_id'];
    public $timestamps = false;


    /*得到头像*/
    public static function getAvatar($id)
    {

        $redisKey = 'avatarKey_' . $id;

        if (Redis::exists($redisKey)) {
            Redis::expire($redisKey, 300);
            return Redis::get($redisKey);
        } else {
            $value = env('imgUrl') . '/' . self::find($id)->avatar_key;
            Redis::set($redisKey, $value);
            Redis::expire($redisKey, 300);
            return $value;
        }

    }


    /*得到头像*/
    public static function getUsername($id)
    {
        $redisKey = 'username_' . $id;

        if (Redis::exists($redisKey)) {
            Redis::expire($redisKey, 300);
            return Redis::get($redisKey);
        } else {
            $username = self::find($id)->username;
            Redis::set($redisKey, $username);
            Redis::expire($redisKey, 300);
            return $username;
        }

    }


    public function dynamic()
    {


        return $this->hasMany('App\Http\Model\Dynamic', 'user_id', 'user_id')->where('state', 0);

    }


    /*查看传入用户是否是当前用户的粉丝*/
    public function isFollowers($id)
    {

        return UserRelation::isAFollowB($id, $this->user_id);
    }


    /*查看传入用户是否是当前用户的关注 */
    public function isFollowing($id)
    {
        return UserRelation::isAFollowB($this->user_id, $id);
    }


}
