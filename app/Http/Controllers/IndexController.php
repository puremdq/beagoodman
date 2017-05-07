<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\Dynamic;
use App\Http\Model\Article;


/*首页控制器*/

class IndexController extends Controller
{
    //

    public function index()
    {

        $dynamics = Dynamic::where([

            ['dynamic_type', '=', 1],
            ['state', '=', 0]

        ])->orderBy('read_num', 'desc')->take(3)->get();



        return view('app.index')->with('dynamics', $dynamics);

    }


}
