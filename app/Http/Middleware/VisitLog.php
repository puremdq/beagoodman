<?php

namespace App\Http\Middleware;

use Closure;

use App\Http\Model\VisitLog as VisitLogModel;
use Illuminate\Support\Facades\Redis;
use App\Lib\Common;

class VisitLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $redis = Redis::connection('default');
        $ip = $_SERVER["REMOTE_ADDR"];

//        if ($ip == '58.251.156.36') {
//
//            return abort('404', '');
//        }


        if (!Redis::exists($ip)) {

            $res = VisitLogModel::create(
                [
                    'ip' => $ip,
                    'visit_time' => date('Y-m-d H:i:s', time()),
                    'ip_city' => Common:: getIpCity()
                ]
            );

            if ($res) {
                $redis->set($ip, 1);//设置访问过
                $redis->expire($ip, 300);//设置过期时间
            }
        }

        return $next($request);
    }


    function getCity($ip)
    {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $ip = json_decode(file_get_contents($url));
        if ((string)$ip->code == '1') {
            return false;
        }
        $data = (array)$ip->data;
        return $data;
    }
}
