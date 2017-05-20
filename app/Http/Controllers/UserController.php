<?php

namespace App\Http\Controllers;

use App\Http\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{


    public function index($idOrName, Request $request)
    {


        $toShow = $request->input('show', 'dynamic');

        if (is_numeric($idOrName)) {
            $user = User::find($idOrName);
        } else {
            $user = User::where('username', $idOrName)->first();
        }

        if ($user == null) {

            return abort(404, '未找到');

        } else {

            return $this->show($user, $toShow, intval($request->input('page', 1)));

        }


    }

    protected function show($user, $toShow, $page)
    {

        $pageSize = 10;
        $count = 0;
        $users = null;

        if ($toShow == 'dynamic') {

            return view('app.user.index')->with([
                'user' => $user,
                'show' => $toShow
            ]);
            //查看当前用户的关注关注
        } else if ($toShow == 'following') {

            $sql = 'select * from `user` WHERE `user`.`user_id` in ( SELECT `target_user_id` FROM  `user_relation` WHERE `source_user_id` = ? ) limit ?,?';
            $countSql = 'select count(*) as `count` from `user` WHERE `user`.`user_id` in (SELECT `target_user_id` FROM  `user_relation` WHERE `source_user_id` = ?)';

            $users = DB::select($sql, [$user->user_id, ($page - 1) * $pageSize, $pageSize]);
            $count = DB::select($countSql, [$user->user_id,]);
            $count = $count[0]->count;


            if ($user->following_num != $count) {
                $user->following_num = $count;
                $user->save();
            }


            //查看当前用户的粉丝
        } else if ($toShow == 'followers') {

            $sql = 'select * from `user` WHERE `user`.`user_id` in (SELECT `source_user_id` FROM  `user_relation` WHERE `target_user_id` = ?) limit ?,?';
            $countSql = 'select count(*) as `count` from `user` WHERE `user`.`user_id` in (SELECT `source_user_id` FROM  `user_relation` WHERE `target_user_id` = ?)';

            $users = DB::select($sql, [$user->user_id, ($page - 1) * $pageSize, $pageSize]);
            $count = DB::select($countSql, [$user->user_id,]);
            $count = $count[0]->count;


            if ($user->followers_num != $count) {
                $user->followers_num = $count;
                $user->save();
            }


        }

        /*    var_dump($count);
            dd($users);*/

        return view('app.user.index')->with([
            'userLists' => $users,
            'show' => $toShow,
            'currentPage' => $page,
            'totalPage' => ceil(intval($count) / $pageSize),
            'user' => $user
        ]);


    }

//    protected function getavatar($user)
//    {
//
//        return redirect(url('getfile') . '?key=' . $user->avatar_key);
//
//
//    }

}
