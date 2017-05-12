<?php

namespace App\Lib;

use App\Http\Model\Notification as UserNotification;
use Illuminate\Support\Facades\Redis;

/**@author puremdq
 * @email puremdq@gmail.com
 * 常用方法类
 */
class Common
{
    /**
     * 给路径添加文件分界符
     * @param string $path 路径
     * @return string
     */
    public static function addDirectorySeparator($path)
    {

        $path = str_replace('\\', '/', $path);

        return (substr($path, -1) == '/') ? $path : $path . '/';
    }


    /**
     * 生成日期的文件路径
     * @param string $baseDir 基础路径
     * @param int $option 生成日期的选项
     * @return string
     */
    public static function getDateDir($baseDir = null, $option = 1)
    {
        switch ($option) {
            case 1:
                $dataPath = date('Y') . '/' . date('m') . '/' . date('d') . '/';
                break;
            case 2:
                $dataPath = date('Ym') . '/' . date('d') . '/';
                break;

            case 3:
                $dataPath = date('Ymd') . '/';
                break;
            default:
                $dataPath = date('Y') . '/' . date('m') . '/' . date('d') . '/';
                break;
        }


        if (!empty($baseDir)) {

            $baseDir = FileUpload::addDirectorySeparator($baseDir);
            $dataPath = $baseDir . $dataPath;
        }

        return $dataPath;
    }

    public static function getIpCity($ip = '')
    {

        $ip = empty($ip) ? self::getIp() : $ip;
        if ($ip == "127.0.0.1") {
            return "本机地址";
        }

        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $ipDataBag = json_decode(@file_get_contents($url));

        if ($ipDataBag->code !== 0) {

            return '未知地址';
        }

        $ipData = $ipDataBag->data;


        if ($ipData->country_id === 'CN') {


            return $ipData->region . $ipData->city . ' ' . $ipData->isp;
        } else {

            return $ipData->country . $ipData->region;

        }
    }


    public static function getIp()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
            $ip = getenv("REMOTE_ADDR");
        } else if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = "unknown";
        }
        return $ip;
    }


    /*查看给定数组是否为空*/
    public static function arrIsNull($arr)
    {

        if (empty($arr)) {

            return true;
        }

        $isNull = true;
        foreach ($arr as $value) {

            if (!empty($value)) {

                $isNull = false;
                break;
            }

        }

        return $isNull;

    }


    /**
     * 添加通知
     * @param int|string $toUserId 要通知的用户id
     * @param string $fromUsername 要通知的用户名
     * @param int|string $fromUserId 发起该消息的用户id
     * @param int|string $notificationType 通知消息类型   //0 赞了 你 动态     1 评论了你的动态  2赞了你的评论  3回复了你的评论  4@le你   5关注了你
     * @param int|string $targetId 产生此条 通知 的记录id (新加的评论  赞)
     * @param int $addOrSub 0添加消息  1删除消息
     * @return string msg 日志信息
     */
    public static function setNotification($toUserId, $fromUserId, $fromUsername, $notificationType = 0, $targetId = null, $addOrSub = 0)
    {

        if ($toUserId == $fromUserId) {

            return '发起者和接受者为同一人不需要添加';
        }

        $redisKey = 'user_' . $toUserId . '_notification';

        if ($addOrSub == 1) {



            $userNotification = UserNotification::where([
                ['to_user_id', '=', $toUserId],
                ['from_user_id', '=', $fromUserId],
                ['notification_type', '=', $notificationType],
                ['target_id', '=', intval($targetId)],
                ['state', '=', 0],
            ])->first();

            $redisData = json_decode(Redis::get($redisKey), true);

            if ($redisData !== null && $userNotification !== null) {
                //只有这样才进行操作


                $userNotification->state = 2;
                if ($userNotification->save()) {

                    $redisData[$notificationType] = $redisData[$notificationType] - 1;

                    if (self::arrIsNull($redisData)) {
                        Redis::del($redisKey);

                    } else {

                        Redis::set($redisKey, json_encode($redisData));

                    }

                    return '';

                } else {

                    return '数据库存入失败';
                }

            } else {

                return '数据存在错误';

            }


        } else if ($addOrSub == 0) {

            $notificationData = [
                'to_user_id' => $toUserId,
                'from_user_id' => $fromUserId,
                'from_username' => $fromUsername,
                'notification_type' => $notificationType,
                'target_id' => $targetId,
                'time' => time()
            ];

            if (UserNotification::create($notificationData)) {

                $redisData = json_decode(Redis::get($redisKey), true);
                if ($redisData !== null) {

                    $currentNum = isset($redisData[$notificationType]) ? $redisData[$notificationType] : 0;

                    $redisData[$notificationType] = $currentNum + 1;

                } else {
                    $redisData = [];
                    $redisData[$notificationType] = 1;

                }

                Redis::set($redisKey, json_encode($redisData));

                return '';

            } else {

                return '数据库写入新消息失败';
            }


        } else {

            return '参数有有误';
        }
    }
}