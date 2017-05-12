<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    //

    public function test()
    {
        Log::info('取消操作成功');
        Log::info('取消操作成功');
        Log::info('取消操作成功');
        Log::info('取消操作成功');
        echo 'ok';

    }


}
