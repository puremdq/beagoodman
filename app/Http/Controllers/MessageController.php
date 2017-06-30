<?php

namespace App\Http\Controllers;

use App\Http\Model\Message;
use App\Http\Model\MessageList;
use App\Http\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Lib\Common;

class MessageController extends Controller
{
    //消息（站内信）控制器


    private function setRead($userId, $otherUserId)
    {

        $messageList = MessageList::where(
            [
                ['user_id1', '=', $userId],
                ['user_id2', '=', $otherUserId]
            ]
        )->orWhere(
            [
                ['user_id1', '=', $otherUserId],
                ['user_id2', '=', $userId]
            ]
        )->first();

        if(!empty($messageList)){
            $messageList->state=1;
            $messageList->save();
        }
    }


    public function index(Request $request)
    {


        /*  'otherUserId': otherUserId,
                            'startTime': startTime,
                            'endTime': endTime,
                            'prependOrAppend': prependOrAppend

        */

        $startTime = intval($request->input('startTime', 1000));
        $endTime = intval($request->input('endTime', time()));
        $otherUserId = intval($request->input('otherUserId'));
        $prependOrAppend = $request->input('prependOrAppend');

        $startTime = ($startTime == 0) ? 1000 : $startTime;
        $endTime = ($endTime == 0) ? time() : $endTime;
        $prependOrAppend = in_array($prependOrAppend, ['prepend', 'append']) ? $prependOrAppend : 'prepend';
        $pageSize = 10;

        $currentUserId = session('user_id');
        $fields = '*';
        $tables = '`message`';
        $where = [
            '((`message`.`from_id` =? AND `message`.`to_id`=? ) OR (`message`.`from_id` =? AND `message`.`to_id`=? ))',
            '`send_time`>?',
            '`send_time`<?'

        ];
        $values = [$otherUserId, $currentUserId, $currentUserId, $otherUserId, $startTime, $endTime];

        $order = $prependOrAppend == 'prepend' ? 'desc' : 'asc';

        $orderBy = '`send_time` ' . $order;
        $limit = 0 . ',' . $pageSize;

        $results = $this->ls($fields, $tables, $where, $values, $orderBy, $limit);


        if (count($results) > 0) {
            $this->setRead($currentUserId,$otherUserId);
            $currentUser = [
                'id' => $currentUserId,
                'username' => User::getUsername($currentUserId),
                'avatar' => User::getAvatar($currentUserId),
            ];
            $otherUser = [
                'id' => $otherUserId,
                'username' => User::getUsername($otherUserId),
                'avatar' => User::getAvatar($otherUserId),
            ];
        } else {

            $currentUser = null;
            $otherUser = null;
        }

        return json_encode(
            [
                'currentUser' => $currentUser,
                'otherUser' => $otherUser,
                'data' => $results,
                'state' => 0
            ]
        );

    }

    public function sendMessage(Request $request)
    {

        /*'to_id': toId,//发送给
                        'message_content': messageContent,//此时会话的id就是通知的id
                        '_token': '{{csrf_token()}}'*/
        $toId = $request->input('to_id');
        $messageContent = $request->input('message_content');

        if (empty($toId) || empty($messageContent)) {

            return json_encode(['state' => -1]);
        }
        $toId = intval($toId);
        $messageContent = trim($messageContent);
        $state = 0;//返回浏览器的状态
        $messageData = null;

        $sendTime = time();
        $fromId = intval(session('user_id'));

        DB::beginTransaction();//开始事务

        try {

            //写入消息
            $previewLength = 50;
            if (mb_strlen($messageContent) <= $previewLength) {

                $messagePreview = $messageContent;
                $messageContent = null;
            } else {
                $messagePreview = mb_substr($messageContent, 0, $previewLength);

            }

            $messageData = Message::create(
                [
                    'message_preview' => $messagePreview,
                    'message_content' => $messageContent,
                    'from_id' => $fromId,
                    'to_id' => $toId,
                    'send_time' => $sendTime]
            );

            //写入通知
            Common::setNotification($toId, $fromId, User::getUsername($fromId), 6, $messageData->message_id, 0, true);
            $messageData = $messageData->toArray();


            //写入最近消息表
            $messageList = MessageList::whereIn('user_id1', [$fromId, $toId])->whereIn('user_id2', [$fromId, $toId])->first();
            if ($messageList == null) {
                //未找到创建
                MessageList::create([
                    'user_id1' => $fromId,
                    'user_id2' => $toId,
                    'message_preview' => $messagePreview,
                    'update_time' => $sendTime

                ]);

            } else {
                //找到更新
                $messageList->message_preview = $messagePreview;
                $messageList->update_time = $sendTime;
                $messageList->state = 0;
                $messageList->save();
            }


            DB::commit();

            //写入

        } catch (\Exception $e) {

            $state = 1;
            DB::rollBack();

        }

        return json_encode([
            'state' => $state,
            'messageData' => $messageData,
            'currentUser' => [
                'id' => $fromId,
                'username' => User::getUsername($fromId),
                'avatar' => User::getAvatar($fromId),
            ],
            'otherUser' => [
                'id' => $toId,
                'username' => User::getUsername($toId),
                'avatar' => User::getAvatar($toId),
            ],

        ]);

    }

}
