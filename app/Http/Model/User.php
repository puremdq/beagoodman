<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use App\Http\Model\UserRelation;

class User extends Model
{


    protected $table = 'user';
    protected $primaryKey = 'user_id';
    protected $guarded = ['user_id'];
    public $timestamps = false;


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
