<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lib\FileUpload;
use Illuminate\Support\Facades\Redis;
use App\Http\Model\File;


class UploadController extends Controller
{
    //

    public function index(Request $request)
    {


        $action = $request->input('action');
        $methodAllow = ['dynamicImgUpload', 'avatarUpload'];

        if (!empty($action) && in_array($action, $methodAllow)) {

            return $this->$action($request->input('key'), $request->input('inputName'));
        } else {
            abort(405, "非法操作");
        }


    }


    /**
     * article图片上传
     * @param string $key 文件存在redis的key
     * @param string $inputName 文件的inputName
     * @return mixed 给客户端的响应
     **/
    private function dynamicImgUpload($key, $inputName)
    {

        $redis = Redis::connection('default');

        //$redis->set("list", "a", "ex", "10");
        $fileUpload = new FileUpload($inputName, ['storeDriver' => 'redis', 'redisKey' => $key, 'expire' => 300], $redis);
        $fileUpload->initialize();
        $res = $fileUpload->execute();

        if ($res) {

            //return back()->with('errorsToAlert', $fileUpload->getErrorMessage());
            $resp = json_encode(array(
                'state' => 0,
                'msg' => '上传成功',
                'url' => url('getfile') . '?key=' . $key,
                'key' => $key
            ));
            return $resp;

        } else {

            $resp = json_encode(array(
                'state' => 1,
                'msg' => $fileUpload->getErrorMessage()

            ));
            return $resp;

        }
    }

}
