<?php

namespace App\Http\Middleware;

use Closure;

use App\Http\Model\VisitLog as VisitLogModel;
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
        $ip = $_SERVER["REMOTE_ADDR"];
        $ipCity = Common::getIpCity($ip);
        if ($ipCity) {

            if (strpos($ipCity, '阿里巴巴')) {

                return abort(404);
            }
        }

        $filterAll = ['101.226.162.90', '111.206.241.76', '125.88.222.250','111.206.221.98','220.178.7.202'];//过滤360网站检测的ip

        if (!in_array($ip, $filterAll)) {

            if (empty(session('isVisit'))) {

                $res = VisitLogModel::create(
                    [
                        'ip' => $ip,
                        'visit_time' => date('Y-m-d H:i:s', time()),
                        'ip_city' => $ipCity
                    ]
                );

                if ($res) {
                    session(['isVisit' => 1]);
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
