<?php
namespace app\Http;

use app\model\Ali;
use app\model\JGuang;
use app\Upload;

/**
 * http 请求地址
 * @author  kevin knmpby@163.com 19-4-17
 */
class Index
{
    /**
     * 请求状态
     * @var string $type
     */
    public $type;

    /**
     * 发送方式
     * @var $string $method enum(file, string (base64))
     */
    public $method;

    /**
     * 云服务储存地址
     * @var string $globleUrl
     */
    public $globleUrl='webSocket/';

    /**
     * 初始化
     */
    public function index(){
        (new JGuang())->push();
        die;
        $func = isset($_GET['s']);
        if (!empty($func)){
            /** 进行页面访问判断 */
            $func = $_GET['s'];
           return (new FriendList())->{$func}();
        }else{
            /** 进行文件上传判断 */
            $this->type = $_GET['type'];
            $this->method = $_GET['setmethod'];
            if ($this->type==2){ //图片
                $res = $this->uploadImage($this->method);
            }else{  //文件
                $res = $this->uploadFile($this->method);
            }
        }
        return $res;
    }

    /**
     * 文件上传
     * @param string $type enum('file', 'base64')
     * @return json
     */
    public function uploadFile($type)
    {
        /** 判断文件类型-选择处理方式 */
        if ($type=='file'){
            $file = Upload::uploadFile($_FILES['file']);
        }else{
            $base64 = $_POST['file'];
            $file = Upload::base64($base64);
        }

        if (!empty($file['fileName'])) {
            /** 源文件目标地址 */
            $url = $this->globleUrl.'file/'.date("Ymd").'/'.$file['fileName'];
            /** 上传原文件 */
            $fileUrl =  (new Ali())->uploadOSS($file['tmp_dir'], $url);
            /** 删除临时文件 */
            if (!empty($fileUrl)){
                unlink($file['tmp_dir']);
                return successJson(['file'=>$fileUrl]);
            }
        }
        return errorJson('上传失败');
    }

    /**
     * 图片上传 - 同时生成缩略图
     * @param  string $type enum('file', 'base64')
     * @return json
     */
    public function uploadImage($type='file')
    {
        /** 判断文件类型-选择处理方式 */
       if ($type=='file'){
           $file = Upload::uploadImg($_FILES['file']);
       }else{
           $base64 = $_POST['file'];
           $file = Upload::base64($base64);
       }
        if (!empty($file['fileName'])) {
            /** 源文件目标地址 */
            $url = $this->globleUrl.'img/'.date("Ymd").'/'.$file['fileName'] . '.' . $file['extorin'];

            /** 缩略文件目标地址 */
            $t_url = $this->globleUrl.'img/'.date("Ymd").'/'."t_".$file['fileName'] . '.' . $file['extorin'];

            /** 源文件上传 */
            $files = (new Ali())->uploadOSS($file['url'], $url);

            /** 缩略图 */
            $file_t = (new Ali())->uploadOSS($file['t_url'], $t_url);

            /** 删除临时文件 */
            if (!empty($files) && !empty($file_t)) {
                unlink($file['url']);
                unlink($file['t_url']);
                return successJson(['file' => $files, 'file_t' => $file_t]);
            }
        }
        return errorJson('上传失败');
    }





}