<?php

namespace App\Http\Controllers;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /*如果给定值未定义 则返回$default 否则返回本身*/
    public function getValue($data, $default = null)
    {
        return isset($data) ? $data : $default;

    }


    /**
     * 构建sql查询 并返回结果
     * @param string $fields 要查询的字段 用逗号隔开 (建议加上``如'`dynamic`.*,`username`,`avatar_key`')
     * @param string $tables 要查询的表 用逗号隔开 (建议加上``如'`dynamic`,`user`')
     * @param array $where 查询条件 每个条件为数组的一条 (如 ['`dynamic`.`user_id`=`user`.`user_id`','`dynamic` . `state` = 0'] )
     * @param array $values 对?的参数绑定 (如[5,6])
     * @param string $orderBy 如'`published_time` desc'
     * @param string $limit 如'0,1000'
     * @return mixed
     * */
    public function ls($fields, $tables, $where, $values = [], $orderBy = null, $limit = null)
    {

        $sql = 'select ' . $fields . ' from ' . $tables . ' where ' . implode(" AND ", $where);

        if (!empty($orderBy)) {

            $sql = $sql . ' order by ' . $orderBy;
        }

        if (!empty($limit)) {

            $sql = $sql . ' limit ' . $limit;
        }

        $results = DB::select(
            $sql,
            $values
        );

        return $results;
    }

//    public function redis($key, $value)
//    {
//
//        $res = Redis::get($key);
//
//        return $res != null ? $res : $value;
//
//    }
}
