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
        $ip = Common::getIp();

        $ipCity = Common::getIpCity($ip);

        if ($ipCity) {

            if (strpos($ipCity, '阿里巴巴') || strpos($ipCity, '阿里云')) {
                return abort(404);
            }
        }

        $filterArr = ['101.226.162.90', '111.206.241.76', '125.88.222.250', '111.206.221.98', '220.178.7.202'];//过滤360网站检测的ip

        if (!in_array($ip, $filterArr)) {
            if (!Redis::exists($ip)) {
                $res = VisitLogModel::create(
                    [
                        'ip' => $ip,
                        'visit_time' => date('Y-m-d H:i:s', time()),
                        'ip_city' => $ipCity
                    ]
                );

                if ($res) {
                    Redis::set($ip, 1);//设置访问过
                    Redis::expire($ip, 600);//设置过期时间
                }
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
