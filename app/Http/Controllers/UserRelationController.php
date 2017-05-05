<?php

namespace App\Http\Controllers;

use App\Http\Model\User;
use Illuminate\Http\Request;
use App\Http\Model\UserRelation;

use Illuminate\Support\Facades\DB;

/*处理用户间关系*/

class UserRelationController extends Controller
{

    /*关注或取消关注*/
    public function toggleFollow(Request $request)
    {
        $targetUserId = $request->input('user_id');

        $sourceUserId = session('user')->user_id;


        $userRelation = UserRelation::where(
            [
                ['source_user_id', '=', $sourceUserId],

                ['target_user_id', '=', $targetUserId]
            ]

        )->first();
        $isCancel = 0;

        DB::beginTransaction();//开始事务

        try {
            if ($targetUserId == $sourceUserId) {

                throw new \Exception('不能关注自己');
            }

            $targetUser = User::findOrFail($targetUserId);
            $sourceUser = User::findOrFail($sourceUserId);

            if ($userRelation == null) {

                /*不存在新建关系*/

                UserRelation::create([
                    'source_user_id' => $sourceUserId,
                    'target_user_id' => $targetUserId,
                    'follow_time' => time()
                ]);


            } else {

                /*在表中存在记录*/

                if ($userRelation->state == 0) {


                    $userRelation->state = 1;
                    $isCancel = 1;//取消关注
                } else {
                    $userRelation->state = 0;

                }

                $userRelation->save();
            }


            if ($isCancel == 0) {


                /*进行的是添加关注操作*/

                $sourceUser->following_num = intval($sourceUser->following_num) + 1;

                $targetUser->followers_num = intval($targetUser->followers_num) + 1;


            } else {


                $sourceUser->following_num = intval($sourceUser->following_num) - 1;
                $targetUser->followers_num = intval($targetUser->followers_num) - 1;

            }

            $sourceUser->save();
            $targetUser->save();

            session(['user' => $sourceUser]);
            DB::commit();

            return json_encode([
                'state' => 0,
                'isCancel' => $isCancel
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();


            return json_encode([
                'state' => 1,
                'msg' => $exception->getMessage(),
                'isCancel' => $isCancel

            ]);


        }

    }
}
