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


    /*  public function create(){

          echo 'sfdsdf';
      }*/


    public function dynamic()
    {


        return $this->hasMany('App\Http\Model\Dynamic', 'user_id', 'user_id')->where('state', 0);

    }


    /*查看传入用户是否是当前用户的粉丝*/
    public function isFollowers($id)
    {
        try {

            UserRelation::where(
                [
                    ['source_user_id', '=', $id],
                    ['target_user_id', '=', $this->user_id],
                    ['state', '=', 0]
                ]
            )->firstOrFail();

            return true;
        } catch (\Exception $exception) {
            return false;

        }
    }


    /*查看传入用户是否是当前用户的关注 */
    public function isFollowing($id)
    {
        try {

            UserRelation::where(
                [
                    ['source_user_id', '=', $this->user_id],
                    ['target_user_id', '=', $id],
                    ['state', '=', 0]
                ]
            )->firstOrFail();

            return true;
        } catch (\Exception $exception) {
            return false;

        }
    }


}
