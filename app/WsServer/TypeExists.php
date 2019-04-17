<?php
namespace app\WsServer;

/**
 * 类型判断
 * @author kevin knmpby@163.com 19-4-17
 */

class TypeExists
{

    /**
     * 判断数据是否为网址
     * @param string $data
     * @return bool
     */
    static function isUrl($data)
    {
        if (self::pregDecide($data)) {
            return self::interDecide($data);
        }

        return false;
    }

    /**
     * 正则匹配网络地址
     * @param string $url
     * @return bool
     */
    static private function pregDecide($url)
    {
        if(preg_match('/^http[s]:\/\/(.*?)$/', $url)){
            return true;
        }

        return false;
    }

    /**
     * 网络请求地址
     * @param string $url
     * @return bool
     */
    static private function interDecide($url)
    {
        if (_curl($url) != false){
            return true;
        }

        return false;
    }

}