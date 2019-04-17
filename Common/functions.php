<?php
/**
 * 公共方法
 * @author kevin knmpby@163.com 19-4-17
 */

/**
 * 错误警告
 * @param string $msg  警告提示
 * @param int $code 错误编码
 * @return array 返回错误编码 和 错误提示
 */
function error($msg, $code=0)
{
    return ['code'=>$code, 'msg'=>$msg];
}

/**
 * json 返回错误提示
 * @param
 * @param int $code 错误编码
 * @return string
 */
function errorJson($msg, $code=0)
{
    return json_encode(['code'=>$code, 'msg'=>$msg]);
}

/**
 * json 返回成功提示
 * @param
 * @param int $code 错误编码
 * @return string
 */
function successJson($msg, $code=1)
{
    return json_encode(['code'=>$code, 'msg'=>$msg]);
}

/**
 * 随机一个cid后缀
 */
function randCid()
{
    $num = [0,1,2,3,4,5,6,7,8,9];
    $numCount = count($num)-1;
    $numKey = rand(0,$numCount);

    $ar = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','w','p','q','r','s','t','w','v','u','x'.'e'.'z'];
    $arCount = count($ar)-1;
    $arKey = rand(0,$arCount);

    $rand = rand(1,2);
    if ($rand==1)
        return $ar[$arKey].$numKey;

    return $num[$numKey].$ar[$arKey];
}

/**
 * curl 网络请求测试
 * @param string $url
 * @return boolean
 */
function _curl($url='')
{
    $ch = curl_init();
    $timeout = 10;
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $contents = curl_exec($ch);
    if (false == $contents) {
       return false;
    }
    return $contents;
}

/**
 * 获取config 配置
 * @param string $configName
 * @return  array
 */
$config = [];
function config($configName)
{
    if (empty($config) || !in_array($configName,array_keys($config))){
       $ini = include __DIR__.'/../config/'.$configName.'.php';
        if ($ini){
            $GLOBALS['config'][$configName] = $ini;
        }
    }
    return $GLOBALS['config'][$configName];
}

