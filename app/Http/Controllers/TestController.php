<?php

namespace App\Http\Controllers;

use App\Lib\Common;

class TestController extends Controller
{
    //

    public function test()
    {

       var_dump( Common::getIpCity('58.19.57.184'));


    }


}
