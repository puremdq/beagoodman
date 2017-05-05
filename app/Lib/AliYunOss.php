<?php

namespace App\Lib;

use OSS\OssClient;
use OSS\Core\OssException;

/**@author puremdq
 * @email puremdq@gmail.com
 * 阿里云对象存储相关方法
 */
class AliYunOss
{

    private static $ossClient = null;
    private static $errorMsg = null;

    /*返回阿里云对象存储实例*/
    public static function getOssClient()
    {
        if (self::$ossClient == null) {

            $accessKeyId = env('accessKeyId');
            $accessKeySecret = env('accessKeySecret');
            $endpoint = env('endpoint');

            try {
                self::$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            } catch (OssException $e) {
                self::$errorMsg = $e->getMessage();
                return false;
            }
        }

        return self::$ossClient;

    }


    /**
     * 上传 文件content
     * @param string $bucket 在云端的实例
     * @param string $objectKey 相当于文件名 唯一
     * @param string $content 文件实体
     * @return boolean
     */
    public static function putObject($bucket, $objectKey, $content)
    {

        $ossClient = self::getOssClient();

        if ($ossClient === false) {
            return false;
        }

        try {
            $ossClient->putObject($bucket, $objectKey, $content);
            return true;

        } catch (OssException $e) {

            self::$errorMsg = $e->getMessage();
            return false;
        }
    }


    /**
     * 上传 文件content
     * @param string $bucket 在云端的实例
     * @param string $objectKey 相当于文件名 唯一
     * @param string $filePath 要上传的本地文件文件名
     * @return boolean
     */
    function uploadFile($bucket, $objectKey, $filePath)
    {

        $ossClient = self::getOssClient();

        if ($ossClient === false) {

            return false;
        }

        try {
            $ossClient->uploadFile($bucket, $objectKey, $filePath);
            return true;
        } catch (OssException $e) {
            self::$errorMsg = $e->getMessage();
            return false;

        }
    }

    public static function getMessage()
    {
        return self::$errorMsg;
    }
}