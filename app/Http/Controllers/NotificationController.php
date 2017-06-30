<?php

namespace App\Http\Controllers;

use App\Http\Model\MessageList;
use App\Http\Model\Notification;
use App\Lib\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/*消息通知控制器*/

class NotificationController extends Controller
{

    //清空redis的通知记录
    private function delRedisNotification($typeInArr)
    {

        $redisKey = 'user_' . session('user_id') . '_notification';

        $redisNotifications = json_decode(Redis::get($redisKey), true);

        if ($redisNotifications != null) {

            foreach ($typeInArr as $type) {

                $redisNotifications[$type] = 0;//清空redis 的消息通知

                if (!Common::arrIsNull($redisNotifications)) {

                    Redis::set($redisKey, json_encode($redisNotifications));
                } else {

                    Redis::del($redisKey);

                }

            }

        }
    }

    public function index(Request $request)
    {
        //0 赞了 你 动态     1 评论了你的动态  2赞了你的评论  3回复了你的评论  4@le你   5关注了你 6有新的站内信
        $currentType = $request->input('type', 'comment');
        $page = intval($request->input('page', 1));

        $pageSize = 10;
        $userId = session('user_id');

        if ($currentType == 'comment') {

            $typeInArr = [1, 3, 4];
            $typeInStr = '1,3,4';
        } else if ($currentType == 'like') {

            $typeInArr = [0, 2];
            $typeInStr = '0,2';

        } else if ($currentType == 'follow') {

            $typeInArr = [5];
            $typeInStr = '5';
        } else if ($currentType == 'message') {

            $typeInArr = [6];
            $typeInStr = '6';
        } else {

            return abort(404);
        }

        if ($currentType != 'message') {


            $notifications = Notification::where('to_user_id', $userId)->whereIn(
                'notification_type', $typeInArr
            )->whereIn(
                'state', [0, 1]
            )->orderBy('time', 'desc')->offset(($page - 1) * $pageSize)->limit($pageSize)->get();

            $count = Notification::where('to_user_id', $userId)->whereIn(
                'notification_type', $typeInArr
            )->whereIn(
                'state', [0, 1]
            )->count();


            if (intval($count) > 0) {

                $sql = "update `notification` set `state` = 1 where `notification_type` in ($typeInStr) AND `to_user_id` = " . $userId;
                //echo $sql;
                $res = DB::update($sql);
                if ($res > 0) {
                    $this->delRedisNotification($typeInArr);

                }
            }

        } else {

            //站内信模块单独处理

            $notifications = MessageList::whereIn('state',[0,1])->where(
                'user_id1', $userId
            )->orWhere(
                'user_id2', $userId
            )->orderBy('update_time', 'desc')->offset(($page - 1) * $pageSize)->limit($pageSize)->get();


            $count = MessageList::where(
                'user_id1', $userId
            )->orWhere(
                'user_id2', $userId
            )->count();
            $this->delRedisNotification($typeInArr);

        }

        return view('app.notifications.index')->with([
            'notifications' => $notifications,
            'currentType' => $currentType,
            'currentPage' => $page,
            'totalPage' => ceil(intval($count) / $pageSize)
        ]);
    }

    public function getNotificationNum()
    {

        $redisKey = 'user_' . session('user_id') . '_notification';

        $data = Redis::get($redisKey);

        if ($data == null) {

            return json_encode([
                'isnull' => 1

            ]);
        } else {

            return json_encode([
                'isnull' => 0,
                'data' => json_decode($data)
            ]);

        }
    }


    /*删除通知*/
    public function delNotificationNum(Request $request)
    {

        DB::beginTransaction();//开始事务
        $state = 0;

        try {
            $notificationId = $request->input('notification_id');
            $notification = MessageList::find($notificationId);
            $notification->state = 2;
            $notification->save();
            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            $state = 1;

        }
        return json_encode([
            'state' => $state
        ]);
    }
}
