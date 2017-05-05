<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Gregwar\Captcha\CaptchaBuilder;
use Session;

class ValidatorController extends Controller
{
    //验证控制器 做验证
    public function index(Request $request)
    {

        $forValidators = ['username', 'email', 'captcha'];
        $data = $request->all();
        $errMsg = null;


        foreach ($data as $key => $value) {

            if (in_array($key, $forValidators)) {

                $res = $this->doValidator($key, $value);

                if ($res) {

                    return response('', 200);

                } else {

                    return response('', 400);

                }

            } else {

                return response('非法的操作', 400);

            }
        }
    }

    private function doValidator($key, $value, &$errMsg = null)
    {

        //$validator = Validator::make($data, $rules, $messages);

        switch ($key) {

            case 'username':

                if (session('user') != null && session('user')->username == trim($value)) {
                    return true;//跟以前一样直接跳过验证
                }

                $validator = Validator::make([$key => trim($value)], ['username' => 'unique:user']);

                if ($validator->fails()) {

                    $errMsg = '用户名已存在';
                    return false;

                } else {

                    return true;
                }


                break;
            case 'email':

                if (session('user') != null && session('user')->email == trim($value)) {
                    return true;//跟以前一样直接跳过验证
                }

                $validator = Validator::make([$key => $value], ['email' => 'unique:user']);

                if ($validator->fails()) {

                    $errMsg = '邮箱已经被注册';
                    return false;

                } else {

                    return true;
                }
                break;
            case 'captcha':

                if (strtolower($value) == session('captcha')) {

                    /*验证码正确*/
                    session(['isRightCaptcha' => 1]);
                    return true;

                } else {
                    session(['isRightCaptcha' => 0]);
                    $errMsg = '验证码输入错误';
                    return false;
                }

                break;

            default: {
                $errMsg = '非法的操作';
                return false;
            }

        }

    }


    /*生成验证码*/
    public function captcha()
    {

        $builder = new CaptchaBuilder();
        //可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        //获取验证码的内容
        $phrase = $builder->getPhrase();


        //把内容存入session
        session(['captcha' => strtolower($phrase)]);
        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();

    }
}
