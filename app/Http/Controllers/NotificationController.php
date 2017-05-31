<?php

namespace App\Http\Controllers;

use App\Http\Model\Notification;
use App\Lib\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/*消息通知控制器*/

class NotificationController extends Controller
{

    public function index(Request $request)
    {
        //0 赞了 你 动态     1 评论了你的动态  2赞了你的评论  3回复了你的评论  4@le你   5关注了你
        $currentType = $request->input('type', 'comment');
        $page = intval($request->input('page', 1));

        $pageSize = 10;

        if ($currentType == 'comment') {

            $typeInArr = [1, 3, 4];
            $typeInStr = '1,3,4';
        } else if ($currentType == 'like') {

            $typeInArr = [0, 2];
            $typeInStr = '0,2';

        } else if ($currentType == 'follow') {

            $typeInArr = [5];
            $typeInStr = '5';
        } else {

            return abort(404);
        }


        $notifications = Notification::where('to_user_id', session('user_id'))->whereIn(
            'notification_type', $typeInArr
        )->whereIn(
            'state', [0, 1]
        )->orderBy('time', 'desc')->offset(($page - 1) * $pageSize)->limit($pageSize)->get();

        $count = Notification::where('to_user_id', session('user_id'))->whereIn(
            'notification_type', $typeInArr
        )->whereIn(
            'state', [0, 1]
        )->count();


        if (intval($count) > 0) {

            $sql = "update `notification` set `state` = 1 where `notification_type` in ($typeInStr) AND `to_user_id` = " . session('user_id');
            //echo $sql;
            $res = DB::update($sql);
            if ($res > 0) {
                $redisKey = 'user_' . session('user_id') . '_notification';

                $redisNotifications = json_decode(Redis::get($redisKey), true);

                if ($redisNotifications != null) {

                    foreach ($typeInArr as $type) {

                        $redisNotifications[$type] = 0;

                        if (!Common::arrIsNull($redisNotifications)) {

                            Redis::set($redisKey, json_encode($redisNotifications));
                        } else {

                            Redis::del($redisKey);

                        }

                    }

                }
            }
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
}
