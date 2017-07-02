<?php

namespace App\Http\Controllers;

use App\Http\Model\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;

class AssistController extends Controller
{


    public function getFile(Request $request)
    {
        $key = $request->input('key', '');

        $type = 'jpeg';

        if (Redis::exists($key)) {
            try {

                $img = Redis::get($key);

            } catch (\Exception $exception) {

                try {

                    $img = (Redis::lrange($key, 0, -1))[0];
                    $name = (Redis::lrange($key . "_name", 0, -1))[0];

                    if (!empty($name)) {
                        $extension = substr(strrchr($name, '.'), 1);
                        if (in_array($extension, array('jpg', 'gif', 'png', 'jpeg'))) {
                            $type = $extension;
                        }
                    }

                } catch (\Exception $e) {

                    //echo $exception->getMessage();
                    return abort(404);
                }

            }
            header('Content-type: image/' . $type);
            echo $img;
            
        } else {

            try {
                $url = '/';
                $redisKey = $key . '_url';

                if (Redis::exists($redisKey)) {
                    $url = Redis::get($redisKey);

                } else {
                    $file = File::where('file_key', $key)->firstOrFail();
                    $url = $file->file_url;
                    Redis::set($redisKey, $url);
                    Redis::expire($redisKey, 3600);//设置过期时间
                }

                return redirect(url($url));

            } catch (\Exception $findException) {

                return abort(404);
            }

        }


    }
}
