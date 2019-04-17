<?php
namespace app\model;

use app\classes\model;

/**
 * 用户表
 * @author  kevin knmpby@163.com 19-4-7
 */
class User
{
    static private $table = 'ws_user';
    /**
     * 根据信息获取用户id
     * @param array
     * @return array | bool
     */
    static function getId($data)
    {
        $where = [];
        foreach ($data as $key=>$value){
            $where['set'] = $key."=:set";
            $where['value'] = $value;
        }
        $res = model::init()->select('*')->from(self::$table)->where($where['set'])->bindValue('set', $where['value'])->row();
        return $res;
    }

    /**
     * 根据信息设置登录状态
     * @param array
     * @param int
     * @return array
     */
    static function setLogin($data,$is_login=1)
    {
        foreach ($data as $key=>$value){
            $where['set'] = $key."=:set";
            $where['value'] = $value;
        }
//        ($is_login==1)?'':
        $time = date("Y-m-d H:i");
        $res = model::init()->update(self::$table)->cols(['is_login'=>$is_login, 'login_time'=>$time])->where($where['set'])->bindValue('set', $where['value'])->row();
        return $res;
    }




}