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


        $dynamics = Dynamic::where('state', '=', 0)->orderBy('published_time', 'desc')->take(10)->get();

        return view('app.index')->with('dynamics', $dynamics);


    }


}
