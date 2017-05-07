<?php

namespace App\Http\Middleware;

use Closure;

use App\Http\Model\VisitLog as VisitLogModel;
use Illuminate\Support\Facades\Redis;

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

        if (!Redis::exists($ip)) {

            $res = VisitLogModel::create(
                ['ip' => $ip, 'visit_time' => date('Y-m-d h:i:s', time())]
            );

            if ($res) {
                $redis->set($ip, 1);//设置访问过
                $redis->expire($ip, 300);//设置过期时间
            }
        }
        return $next($request);
    }
}
