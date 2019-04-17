<?php

namespace app\model;
use OSS\OssClient;

/**
 * 阿里云-model
 * @author kevin knmpby@163.com   19-4-17
 */
class Ali{

    /** 阿里云配置 */
    private $ali;

    function __construct()
    {
        $this->ali = config('ali');
    }

    /**
     * 文件上传
     * @throws $ossClient
     * @throws $ossClient
     * @param  string 源文件位置
     * @param  string 目标文件位置
     * @return string
     */
    public function uploadOSS($file, $object){
        $config=$this->ali;
        $ossClient= new OssClient($config['key'],$config['secret'],$config['oss']['endpoint']);
        $bucket="websocket";
        $res=$ossClient->uploadFile($bucket,$object,$file);
        if(!$res['info']['url']){
            return false;
        }
        return $res['info']['url'];
    }

//    /**
//     * 获取阿里oss签名
//     * return array
//     */
//    public function ossSign(){
//        $config=$this->ali;
//        $now = time();
//        $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
//        $end = $now + $expire;
//        $expiration = gmt_iso8601($end);
//        $condition = array(0=>'content-length-range', 1=>0, 2=>1048576000);
//        $conditions[] = $condition;
//        $start = array(0=>'starts-with', 1=>'$key', 2=>'');
//        $conditions[] = $start;
//        $arr = array('expiration'=>$expiration,'conditions'=>$conditions);
//        $policy = json_encode($arr);
//        $base64_policy = base64_encode($policy);
//        $string_to_sign = $base64_policy;
//        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $config['secret'], true));
//        $response = array();
//        $response['accessid'] = $config['key'];
//        $response['host'] = $config['oss']['request'];
//        $response['policy'] = $base64_policy;
//        $response['signature'] = $signature;
//        $response['expire'] = $end;
//        $response['dir'] = '';
//        return $response;
//    }
}