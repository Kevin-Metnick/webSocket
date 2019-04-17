<?php
namespace app\model;

use app\classes\model;

/**
 * 用户关系-model
 * @author keivn knmpby@163.com 19-4-17
 */
class UserRelation
{
    /**
     * 数据表-名称
     */
    static private $table = 'ws_user_relation';

    /**
     * 根据信息获取用户id
     * @throws $res
     * @param array $data 条件
     * @return array
     */
    static function getList(array $data)
    {
        $where = [];
        $res = model::init()->select('*')->from(self::$table)->leftJoin('ws_user','ws_user.id=`ws_user_relation`.user_id and ws_user.is_login=1');
        foreach ($data as $key=>$value){
            $where['key'] = $key."=:".$key;
            $where['value'] = $value;
            $res = $res->where($where['key'])->bindValue($key,$where['value']);
        }
        $result = $res->query();
        return $result;
    }

    /**
     * 获取消息列表
     * @throws $res
     * @param array $data
     * @param array $order
     * @return false;
     */
    static function getMessageList($data, $order=['add_time'])
    {
        $where = [];
        $res = model::init()->select('*')->from(self::$table)->leftJoin('ws_user','ws_user.id=`ws_user_relation`.user_id');
        foreach ($data as $key=>$value){
            $where['key'] = $key."=:".$key;
            $where['value'] = $value;
            $res = $res->where($where['key'])->bindValue($key,$where['value']);
        }
        $res = $res->orderByASC($order);
        $result = $res->query();
        return $result;
    }


}