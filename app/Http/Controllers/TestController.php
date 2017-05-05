<?php

namespace App\Http\Controllers;

use Mail;
use OSS\OssClient;
use OSS\Core\OssException;

class TestController extends Controller
{
    //

    public function test()
    {

        /*   $name = 'pure';
           $flag = Mail::send(['text' => '你的验证码是072506'], ['name' => $name], function ($message) {
               $to = '991458902@qq.com';
               $message->to($to)->subject('beagoodman.cn感谢注册我们的网站');
           });*/


        $accessKeyId = env('accessKeyId');
        $accessKeySecret = env('accessKeySecret');
        $endpoint = env('endpoint');

        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

            var_dump($ossClient->putObject('puremdq', 'hhh', 'ggggg'));

        } catch (OssException $e) {

            echo 'sdf';
            print $e->getMessage();
        }


    }

    public function aliyun()
    {


    }
}
