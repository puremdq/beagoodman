<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    //

//    public function test()
//    {
//        Log::info('取消操作成功');
//        Log::info('取消操作成功');
//        Log::info('取消操作成功');
//        Log::info('取消操作成功');
//        echo 'ok';
//
//    }

    public function foTeacher()
    {


        $url = 'http://w.api.xiaoying.co/webapi2/rest/video/like?callback=videocallbacklikecountadd&appkey=30000000&puid=m7wzk&ver=1&format=MP4&_=1494729099217';

        $counts = intval($_POST['counts']);

        $counts > 0 ? $counts : 10;
        for ($i = 0; $i < $counts; $i++) {
            $res = file_get_contents($url);
            $res = substr($res, strpos("$res", "(") + 1);

            $res = substr($res, 0, -1);

            $res = json_decode($res, true);



            $liketime = $i + 1;

            echo '此次点赞' . $liketime . '次    当前已有' . $res['likeinfo']['likecount'] . '个赞</br>';
            //var_dump($res);

        }


    }


}
