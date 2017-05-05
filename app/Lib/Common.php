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


//    /**
//     * 相对路径
//     * @param string $path1
//     * @param int $path2
//     * @return string
//     */
//    public static function getRelativePath($path1, $path2)
//    {
//        $path1 = str_replace('\\', '/', $path1);
//        $path2 = str_replace('\\', '/', $path2);
//
//
//    }
}