<?php

namespace App\Http\Controllers;

use App\Http\Model\User;
use Illuminate\Http\Request;

class UserController extends Controller
{


    public function index($idOrName, Request $request)
    {


        if (is_numeric($idOrName)) {
            $user = User::find($idOrName);
        } else {
            $user = User::where('username', $idOrName)->first();
        }

        if ($user == null) {

            return abort(404, '未找到');

        } else {

            $actionAllow = ['getavatar', 'show'];
            $action = $this->getValue($request->input('action'), 'show');

            if (in_array($action, $actionAllow)) {

                return $this->$action($user);
            } else {
                return abort(404, '非法的操作');

            }

        }


    }

    protected function show($user)
    {

        return view('app.user.index')->with('user', $user);


    }

    protected function getavatar($user)
    {

        return redirect(url('getfile') . '?key=' . $user->avatar_key);


    }

}
