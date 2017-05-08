<?php

namespace App\Lib;

/**@author puremdq
 * @email puremdq@gmail.com
 * 常用方法类
 */
class Common
{
    /**
     * 给路径添加文件分界符
     * @param string $path 路径
     * @return string
     */
    public static function addDirectorySeparator($path)
    {

        $path = str_replace('\\', '/', $path);

        return (substr($path, -1) == '/') ? $path : $path . '/';
    }


    /**
     * 生成日期的文件路径
     * @param string $baseDir 基础路径
     * @param int $option 生成日期的选项
     * @return string
     */
    public static function getDateDir($baseDir = null, $option = 1)
    {
        switch ($option) {
            case 1:
                $dataPath = date('Y') . '/' . date('m') . '/' . date('d') . '/';
                break;
            case 2:
                $dataPath = date('Ym') . '/' . date('d') . '/';
                break;

            case 3:
                $dataPath = date('Ymd') . '/';
                break;
            default:
                $dataPath = date('Y') . '/' . date('m') . '/' . date('d') . '/';
                break;
        }


        if (!empty($baseDir)) {

            $baseDir = FileUpload::addDirectorySeparator($baseDir);
            $dataPath = $baseDir . $dataPath;
        }

        return $dataPath;
    }

    public static function getIpCity($ip = '')
    {

        $ip = empty($ip) ? self::getIp() : $ip;
        if ($ip == "127.0.0.1") {
            return "本机地址";
        }

        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $ipDataBag = json_decode(@file_get_contents($url));

        if ($ipDataBag->code !== 0) {

            return '未知地址';
        }

        $ipData = $ipDataBag->data;


        if ($ipData->country_id === 'CN') {


            return $ipData->region . $ipData->city . ' ' . $ipData->isp;
        } else {

            return $ipData->country . $ipData->region;

        }
    }


    public static function getIp()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
            $ip = getenv("REMOTE_ADDR");
        } else if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = "unknown";
        }
        return $ip;
    }
}