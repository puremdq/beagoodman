<?php

namespace App\Lib;

/**@author puremdq
 * @email puremdq@gmail.com
 * @file: FileUpload.class.php 文件上传类FileUpload
 * 本类的实例对象用于处理上传文件，可以上传一个文件，也可同时处理多个文件上传(可设置某个文件出错后是否终止上传)
 * 支持将文件用list的方式存入redis
 */
class FileUpload
{
    protected $config = array(
        'allowType' => array('jpg', 'gif', 'png', 'jpeg'),
        'maxsize' => 2000000,                      //限制文件上传大小（字节B）
        'isRandName' => true,                      //设置是否随机重命名文件， false 则为原文件名
        'interrupt' => false,                        //出现错误时  是否终止操作

        'allowStoreDriver' => array('tradition', 'redis'),// 允许的存储方式
        'storeDriver' => 'disk',              //存储方式  disk 存储到磁盘  redis存储到 redis

        'uploadPath' => './uploads/',        //上传文件保存的路径

        'redisKey' => null,                 //存储到redis 的key
        'expire' => 300                     //可选 redis的过期时间(s)  0表示永不过期
    );

    protected $errorMessages = array(
        0 => '',
        1 => '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
        2 => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。',
        3 => '文件只有部分被上传。',
        4 => '没有文件被上传。',
        6 => '没有指定临时文件夹',
        7 => '文件写入失败',
        8 => '文件上传被服务器中其他扩展阻止',
        9 => '其他已知错误',
        10 => '未知错误',
        11 => 'redis存入错误'
    );

    protected $fileInputName = 'file';   //表单中file标签的 name值


    protected $file = null;                 //上传的文件
    protected $driverObj = null;              //存入介质驱动实例(如 redis 对象 磁盘驱动对象)

    protected $fileName = null;                 //源文件名（文件本身的名字）
    protected $tmpFileName = null;              //临时文件名(绝对)
    protected $fileSize = null;                 //文件大小
    protected $fileExtension = null;                  //文件类型(文件后缀)
    protected $type = null;        //文件类型

    protected $newFileName = null;              //新文件名
    protected $errorCode = 0;            //第一个 最后一个出错文件的错误号
    protected $errorMessage = '';        //错误报告消息


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
     * 构造函数
     * @param  string $fileInputName 表单中file标签的 name值
     * @param  array $config 配置项(不区分大小写)
     * @param  mixed $driverObj 要存入介质的驱动
     * @param  array $errorMessages 错误代码与错误信息的对应关系
     *
     */
    public function __construct($fileInputName = 'files', $config = null, $driverObj = null, $errorMessages = null)
    {
        $this->fileInputName = $fileInputName;
        $this->driverObj = $driverObj;

        if (is_array($config)) {

            $this->config = $config + $this->config;

        }

        if (is_array($errorMessages)) {

            $this->errorMessages = $errorMessages + $this->errorMessages;

        }
    }


    /**
     * 初始化
     * @return boolean
     */
    public function initialize()
    {

        if (!in_array($this->getConfig('storeDriver'), $this->getConfig('allowStoreDriver'))) {

            $this->errorCode = 9;
            $this->setVariable('errorMessage', '不支持的写入驱动');
            return false;
        }

        if (isset($_FILES[$this->fileInputName])) {

            $file = $_FILES[$this->fileInputName];

            $this->file = $file;
            $this->fileName = $file['name'];
            $this->tmpFileName = $file['tmp_name'];
            $this->fileSize = $file['size'];             //字节B
            $this->errorCode = $file['error'];
            $this->type = $file['type'];

            return true;


        } else {
            $this->errorCode = 9;
            $this->setVariable('errorMessage', '无效的file键值');
            return false;
        }


    }


    /**
     * 执行上传操作
     * @return mixed 错误返回false 成功则返回文件存储的新的绝对路径
     */

    public function execute()
    {

        if ($this->errorCode != 0) {

            return false;
        }

        $interrupt = $this->getConfig('interrupt');//是否在 出错时中断

        if (is_array($this->fileName)) {

            $flag = true;

            for ($i = 0; $i < count($this->fileName); $i++) {

                if (!$this->handelUpload($this->fileName[$i], $i)) {

                    $flag = false;
                    if ($interrupt) {
                        return $flag;
                    }

                }
            }

            return $flag;

        } else {
            return $this->handelUpload($this->fileName);
        }


    }


    /**
     * 具体的执行上传操作*(存储)
     * @param string $fileName 文件名
     * @param int $i 第i 个
     * @return boolean 是否成功
     */

    protected function handelUpload($fileName = null, $i = null)
    {

        $fileExtension = $this->getFileExtension($this->type, $fileName, $i);
        $fileSize = $this->getVariable('fileSize', $i);

        if (!$this->checkFile($fileSize, $fileExtension, $i)) {
            return false;
        }

        $tmpFileName = $this->getVariable('tmpFileName', $i);
        $newFileName = $this->getNewFileName($fileName, $fileExtension, $i);

        return $this->store($tmpFileName, $newFileName, $this->getConfig('storeDriver'));

    }


    /**
     * 将文件存入
     * @param $tmpFileName string 文件的临时目录
     * @param $newFileName string 新文件名
     * @param $storeDriver string 存入的磁盘还是redis
     * @param $i mixed 第$i个
     * @return boolean 是否成功
     * */
    protected function store($tmpFileName, $newFileName, $storeDriver = 'tradition', $i = null)
    {

        switch ($storeDriver) {

            case 'tradition':
                /*传统方式存储*/
                $uploadPath = FileUpload::addDirectorySeparator($this->getConfig('uploadPath'));

                if (!@is_dir($uploadPath)) {
                    if (!@mkdir($uploadPath, 0622, true)) {
                        $this->setVariable('errorCode', 9, $i);
                        $this->setVariable('errorMessage', '不是正确的上传路径', $i);
                        return false;
                    }
                }

                $newAbsolutePath = $uploadPath . $newFileName;
                if (@move_uploaded_file($tmpFileName, $newAbsolutePath)) {
                    return true;
                } else {
                    $this->setVariable('errorCode', 9, $i);
                    $this->setVariable('errorMessage', '文件移动出错', $i);
                    return false;
                }
                break;


            case 'redis':

                $redis = $this->driverObj;
                if ($redis === null) {
                    $this->setVariable('errorCode', 9, $i);
                    $this->setVariable('errorMessage', '未找到redis驱动', $i);
                }

                $data = file_get_contents($tmpFileName);
                $redisKey = $this->getConfig('redisKey');
                $expire = $this->getConfig('expire');

                if ($redisKey === null) {

                    $this->setVariable('errorCode', 9, $i);
                    $this->setVariable('errorMessage', '未设置redisKey', $i);
                }

                try {
                    $redis->rpush($redisKey, $data);    //存储文件

                    /*TODO 将文件名的key作为配置参数传入*/

                    $filenameRedisKey = $redisKey . "_name";

                    $redis->rpush($filenameRedisKey, $newFileName);//存储文件名

                    $redis->expire($redisKey, $expire);//设置过期时间
                    $redis->expire($filenameRedisKey, $expire);//设置过期时间

                    return true;

                } catch (\Exception $exception) {

                    $this->setVariable('errorCode', 11, $i);
                    $this->setVariable('errorMessage', '不能连接到redis', $i);
                    return false;
                }

                break;

        }
    }


    /**
     * 检查文件是否允许上传
     * @param int $fileSize 要检查的文件大小
     * @param string $fileExtension 要检查的文件的后缀
     * @param int $i
     * @return boolean 是否为合法的文件
     **/
    protected function checkFile($fileSize = null, $fileExtension = null, $i = null)
    {

        $errorCode = $this->getVariable('errorCode', $i);


        if ($errorCode !== 0) {
            $this->setVariable('errorMessage', $this->getErrorMessage($errorCode), $i);
            return false;
        }
        $fileSize = isset($fileSize) ? $this->getVariable('fileSize', $i) : $fileSize;
        $fileExtension = empty($fileExtension) ? $this->getVariable('fileExtension', $i) : $fileExtension;


        if ($fileSize > ($this->getConfig('maxsize'))) {
            $this->setVariable('errorCode', 9, $i);
            $this->setVariable('errorMessage', '文件过大', $i);
            return false;
        }


        if (!in_array($fileExtension, $this->getConfig('allowType'))) {
            $this->setVariable('errorCode', 9, $i);
            $this->setVariable('errorMessage', '不支持的文件格式', $i);
            return false;
        }

        return true;
    }


    /**
     * 返回文件后缀
     * @param string $type 文件类型
     * @param string $fileName 文件名
     * @param int $i
     * @return string 文件后缀
     * */
    protected function getFileExtension($type, $fileName = null, $i = null)
    {

        $temp = $this->getVariable('fileExtension', $i);

        if (isset($temp)) {
            return $temp;
        } else {

            $type = isset($type) ? $type : $this->getVariable('type', $i);
            $fileExtension = substr(strrchr($type, '/'), 1);

            if ($fileExtension == false) {

                $fileName = isset($fileName) ? $fileName : $this->getVariable('fileName', $i);
                $fileExtension = substr(strrchr($fileName, '.'), 1);
            }

            $this->setVariable('fileExtension', $fileExtension, $i);
            return $fileExtension;
        }

    }


    /**
     * 生成并返回新的文件名
     * @param string $fileName 文件名
     * @param string $fileExtension 文件后缀
     * @param int $i 如果是多个文件，i表示第i个 i从0开始
     * @return string
     **/
    protected function getNewFileName($fileName = null, $fileExtension = null, $i = null)
    {

        $newFileName = $this->getVariable('newFileName', $i);

        if (empty($newFileName)) {

            $fileName = isset($fileName) ? $fileName : $this->getVariable('fileName', $i);
            $fileExtension = isset($fileExtension) ? $fileExtension : $this->getVariable('fileExtension', $i);

            $isRandName = $this->getConfig('isRandName');
            $isRandName = isset($isRandName) ? boolval($isRandName) : true;

            if ($isRandName) {
                $newFileName = time() . '_' . rand(0, 100) . '.' . $fileExtension;
            } else {
                $newFileName = $this->$fileName;
            }

            $this->setVariable('newFileName', $newFileName, $i);
        }

        return $newFileName;
    }


    /**@param  int $errorCode
     * @return string 错误信息
     * */
    public function getErrorMessage($errorCode = null)
    {
        $errorMessages = $this->errorMessages;

        if (isset($errorCode)) {

            //当 $errorCode=0时 会进入此语句返回''
            return isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : '未知错误';

        } else {

            $errorMessage = $this->errorMessage;

            if (!empty($errorMessage)) {

                if (is_array($errorMessage)) {
                    $errorMsg = '';
                    foreach ($errorMessage as $key => $value) {
                        $temp = $key + 1;
                        $errorMsg = $errorMsg . " 文件[$temp]上传失败($value)   ";
                    }

                    if ($this->getConfig('interrupt')) {
                        $errorMsg = $errorMsg . '已经终止后续文件上传操作';
                    }

                    return $errorMsg;

                } else {
                    return $errorMessage;
                }


            } else {

                return '';
            }

        }

    }


    /**
     * 得到变量值
     * @param string $variableName 变量名 必选
     * @param int $i
     * @return mixed
     *
     * */
    public function getVariable($variableName, $i = null)
    {

        $fitterArr = array(
            'config', 'errorMessages', 'fileInputName', 'file'
        );


        if (isset($this->$variableName)) {

            if (in_array($variableName, $fitterArr) || $i === null) {
                return $this->$variableName;

            } else {

                return isset($this->$variableName[$i]) ? $this->$variableName[$i] : null;

            }
        } else {
            return null;
        }

    }


    /**
     * 设置变量值
     * @param string $variableName 变量名 必选
     * @param mixed $value 变量值 可选
     * @param int $i
     * */
    protected function setVariable($variableName, $value = null, $i = null)
    {

        $fitterArr = array(
            'config', 'errorMessages', 'fileInputName', 'file'
        );


        if (in_array($variableName, $fitterArr)) {
            $this->$variableName = $value;

        } else {
            if (isset($i)) {
                $this->$variableName[$i] = $value;
            } else {
                $this->$variableName = $value;
            }
        }

    }

    /**
     *返回配置信息
     * @param string $configName
     * @return mixed
     */
    public function getConfig($configName)
    {
        $config = $this->config;
        return isset($config[$configName]) ? $config[$configName] : null;
    }

    /**
     *魔术方法  __get
     * @param string $varName
     * @return mixed
     */
    public function __get($varName = null)
    {
        if (!isset($varName))
            return null;
        else
            return isset($this->$varName) ? $this->$varName : null;
    }

}