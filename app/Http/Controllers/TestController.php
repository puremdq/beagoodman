<?php

namespace App\Http\Controllers;

use App\Lib\Common;

class TestController extends Controller
{
    //

    public function test()
    {

        Common::getIpCity('223.104.107.174');


    }


}
