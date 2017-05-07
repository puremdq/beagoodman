<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Model\User;

class AuthController extends Controller
{


    /*登录*/
    public function login(Request $request)
    {

        $data = $request->all();

        $user = User::where('username', $data['username'])->first();

        $user = ($user === null) ? User::where('email', $data['username'])->first() : $user;

        if ($user == null) {

            return back()->with(['state' => -1, 'tips' => '不存在该用户']);
        }

        if ($user->password == md5($data['password'] . $user->salt)) {

            session(['user' => $user]);
            session(['user_id' => $user->user_id]);
            return back()->with(['state' => 1, 'tips' => '登录成功']);

        } else {

            /* return json_encode([
                 'state'=>1,
                 'msg'=>'用户名或密码错误'
             ]);*/
            return back()->with(['state' => -1, 'tips' => '用户名或密码错误']);
        }

    }

    public function register(Request $request)
    {

        //dd($request->all());

        //$validator = Validator::make($input, $rules, $messages);

        if (session('isRightCaptcha') === null || session('isRightCaptcha') == 0) {

            /*验证码不对结束注册*/
            return json_encode([
                'state' => -1,
                'msg' => '你能到这儿还是很机智的'

            ]);

        } else {
            //忘记验证码已经 正确的凭证
            session()->forget('isRightCaptcha');

        }

        $data = $request->except(['_token', 'captcha']);

        if (is_numeric(($request->input('username'))[0])) {

            return json_encode([
                'state' => 1,
                'msg' => '不能是数字开头'

            ]);
        }

        //var_dump($data);

        $rules = [
            'username' => 'bail|required|max:15|unique:user',
            'email' => 'bail|email|max:80|unique:user',
            'password' => 'bail|required|between:6,16|confirmed',
            'password_confirmation' => 'bail|required',

        ];

        $messages = [
            'username.unique' => '用户名已存在',
            'email.unique' => '邮箱已经被注册',
            'password.required' => '新密码不能为空',
            'password.between' => '请确保密码为6--16个字符',
            'password_confirmation.required' => '请再次输入密码',
            'password.confirmed' => '两次密码不一致',
        ];


        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {

            return json_encode([
                'state' => 1,

            ]);

        } else {

            unset($data['password_confirmation']);
            $data['salt'] = str_random(6);
            $data['password'] = md5($data['password'] . $data['salt']);
            $res = User::create($data);

            //var_dump($res);

            if ($res) {

                /*注册成功*/
                session(['user' => $res]);//将用户写入session;
                session(['user_id' => $res->user_id]);

                return json_encode([
                    'state' => 0,

                ]);

            } else {
                return json_encode([
                    'state' => 1,

                ]);

            }

        }

    }

    public function logout()
    {
        session()->forget('user');

        return back()->with(['state' => 0, 'tips' => '已安全退出']);

    }


}
