<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

/*用户关系(关注) model*/

class UserRelation extends Model
{
    protected $table = 'user_relation';
    protected $primaryKey = 'relation_id';
    protected $guarded = ['relation_id'];
    public $timestamps = false;


    /**
     *查看a是否关注b
     * @param $aid
     * @param $bid
     * @return boolean
     */
    public static function isAFollowB($aid, $bid)
    {

        try {
            self::where(
                [
                    ['source_user_id', '=', $aid],
                    ['target_user_id', '=', $bid],
                    ['state', '=', 0]
                ]
            )->firstOrFail();

            return true;
        } catch (\Exception $exception) {
            return false;

        }
    }
}
