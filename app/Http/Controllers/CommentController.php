<?php

namespace App\Http\Controllers;

use App\Http\Model\Dynamic;
use App\Http\Model\LikeRecord;
use Illuminate\Http\Request;
use App\Http\Model\Comment;

use Illuminate\Support\Facades\DB;

/*评论控制器*/

class CommentController extends Controller
{
    /*得到评论*/
    public function index(Request $request)
    {

        /*TODO 将页面大小设为配置项*/
        $dynamicId = $request->input('dynamic_id');
        $page = intval($this->getValue($request->input('page'), 1));
        $offset = intval($this->getValue($request->input('offset'), 0));
        $commentPid = intval($this->getValue($request->input('comment_pid'), 0));

        $pageSize = $commentPid == 0 ? 15 : 5;

        /*TODO 验证权限  考虑用户已被删除的情况*/

        $fields = '`comment`.*,`username`,`avatar_key`';
        $tables = '`comment`,`user`';
        $where = [
            '`comment`.`state`=0',
            '`comment`.`user_id`=`user`.`user_id`',
            '`comment_target` = ?',
            '`comment`.`comment_pid`=?'

        ];
        $orderBy = '`comment_time` desc';
        $limit = ($pageSize * ($page - 1) + $offset) . ',' . $pageSize;
        $values = [$dynamicId, $commentPid];

        $results = $this->ls($fields, $tables, $where, $values, $orderBy, $limit);

        $fields = 'count(`comment`.`comment_id`) as `count`';
        $count = $this->ls($fields, $tables, $where, $values);
        $count = $count[0]->count;//得到数量

        return json_encode(
            [
                'state' => 0,
                'commentList' => $results,
                'isAll' => $pageSize * $page + $offset >= $count ? 1 : 0

            ]
        );

    }


    /*存储评论*/
    public function store(Request $request)
    {
        $data = $request->except('_token');
        $data['user_id'] = session('user')->user_id;
        $data['comment_time'] = time();
        DB::beginTransaction();//开始事务

        try {

            if ($data['comment_pid'] != 0) {
                DB::update('update `comment` set `reply_num`=`reply_num`+1 where `comment_id` = ?', [$data['comment_pid']]);
            }

            $comment = Comment::create($data);
            $dynamic = $comment->target;
            $dynamic->comment_num = $dynamic->comment_num + 1;
            $dynamic->save();
            DB::commit();

            return json_encode([
                    'state' => 0,
                    'avatar_key' => session('user')->avatar_key,
                    'username' => session('user')->username,
                    'user_id' => session('user')->user_id,
                    'comment_content' => $data['comment_content'],
                    'comment_time' => $data['comment_time'],
                    'comment_id' => $comment->comment_id,
                    'comment_pid' => $data['comment_pid'],
                    'like_num' => 0,
                    'reply_num' => 0
                ]
            );

        } catch (\Exception $e) {

            DB::rollBack();
            // echo $e->getMessage();
            return json_encode([
                    'state' => 1,
                    'msg' => $e->getMessage()
                ]
            );

        }

    }


    /*删除评论*/
    protected function delete($id)
    {

        $state = 0;
        $msg = 'success';

        $userId = session('user')->user_id;

        $comment = Comment::where([
            ['comment_id', '=', $id],
            ['user_id', '=', $userId]

        ])->first();

        if ($comment == null) {

            return json_encode([
                'state' => 1,
                'msg' => '非法操作'
            ]);
        }


        DB::beginTransaction();//开始事务

        try {

            $comment->state = 1;
            $commentTarget = $comment->target;//得到 该条评论评论的目标(动态)
            $commentTarget->comment_num = $commentTarget->comment_num - $comment->reply_num - 1;

            $comment->save();
            $commentTarget->save();

            if ($comment->comment_pid != 0) {
                $pComment = Comment::find($comment->comment_pid);
                $pComment->reply_num--;
                $pComment->save();
            }

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();
            $state = 1;
            $msg = $e->getMessage();
        }


        return json_encode([
            'state' => $state,
            'msg' => $msg
        ]);
    }

    public function handleComment(Request $request, $id)
    {

        $allowActionArr = ['delete'];
        $action = $request->input('action');

        if (in_array($action, $allowActionArr)) {

            return $this->$action($id);

        } else {

            return json_encode(
                [
                    'state' => 1,
                    'msg' => '非法操作'
                ]);

        }
    }
}
