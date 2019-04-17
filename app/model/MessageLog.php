<?php
namespace app\model;

use app\classes\model;

/**
 * 消息历史表 - model
 * @author kevin knmpby@163.com 19-4-17
 */
class MessageLog
{
    /**
     * 消息表
     * @var string
     */
    static private $table = 'ws_message_log';

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
        $res = model::init()->select('id')
                            ->from(self::$table)
                            ->where($where['set'])
                            ->bindValue('set', $where['value'])
                            ->row();
        return $res;
    }

    /**
     * @获取用户未读消息列表
     * @param int $id
     * @return array
     */
    static function getHisList($id)
    {
        $res = model::init()->select('*')
                            ->from(self::$table)
                            ->where(' receive=:receive ')
                            ->where(' readStatus=:readStatus ')
                            ->bindValue('receive', $id)
                            ->bindValue('readStatus', 1)
                            ->orderByASC(['send_time'])
                            ->query();
        return $res;
    }

}