<?php

namespace App\Http\Controllers;

use App\Http\Model\Comment;
use App\Http\Model\Dynamic;
use App\Http\Model\LikeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


/*点赞 查看是否有点赞记录相关控制*/

class LikeRecordController extends Controller
{
    /*点赞 或取消赞*/
    public function likeToggle(Request $request)
    {

        $targetId = $this->getValue($request->input('target_id'), 0);
        $targetType = $request->input('target_type');

        $isCancel = 0; //是否是取消赞

        $likeTarget = null;
        if ($targetType == 0) {
            //给动态进行操作

            $likeTarget = Dynamic::find($targetId);
        } else if ($targetType == 1) {
            //给评论进行操作

            $likeTarget = Comment::find($targetId);

        }

        if (empty($likeTarget)) {

            return json_encode(
                [
                    'state' => 1,
                    'msg' => '非法操作'
                ]);

        }

        $likeRecord = LikeRecord::where([
            ['target_type', '=', $targetType],
            ['target_id', '=', $targetId],
            ['user_id', '=', session('user')->user_id]
        ])->first();

        DB::beginTransaction();//开始事务

        try {

            if ($likeRecord != null) {

                /*有记录*/
                if ($likeRecord->state == 0) {

                    /*已经点过赞 进行取消赞操作*/
                    $likeRecord->state = 1;
                    $likeTarget->like_num = $likeTarget->like_num - 1;

                    $isCancel = 1;

                } else if ($likeRecord->state == 1) {
                    /*已经取消赞 进行再次点赞*/

                    $likeRecord->state = 0;
                    $likeTarget->like_num = $likeTarget->like_num + 1;


                } else {
                    /*一般到不了这里*/
                    throw new \Exception('非法操作');

                }

                $likeRecord->save();
                $likeTarget->save();


            } else {
                /*没有记录 进行第一次点赞*/
                $data = [
                    'target_id' => intval($targetId),
                    'target_type' => $targetType,
                    'user_id' => session('user')->user_id,
                    'operate_time' => time()
                ];

                LikeRecord::create($data);

                $likeTarget->like_num = $likeTarget->like_num + 1;
                $likeTarget->save();
            }


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
