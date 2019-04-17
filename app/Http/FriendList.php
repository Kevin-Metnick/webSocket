<?php
namespace app\Http;

use app\model\User;
use app\model\UserRelation;

/**
 * http - 访问
 * @author kevin knmpby@163.com
 */
class FriendList
{
    /**
     * 获取用户信息
     * @return string json
     */
    function getUserInfo()
    {
        $id = $_GET['id'];
        $info = User::getId(['id'=>$id]);
        $json = json_encode($info);
        return $json;
    }

    /**
     * 获取朋友列表
     * @return string json
     */
    function getFriendListAll()
    {
        $id = $_GET['id'];
        $list = UserRelation::getMessageList(['user_id'=>$id]);
        $json = json_encode($list);
        return $json;
    }

    /**
     * 获取消息列表
     * @return string json
     */
    function getMessageList()
    {
        $id = $_GET['id'];
        $list = UserRelation::getMessageList(['user_id'=>$id,'is_login'=>1], ['is_up']);
        $json = json_encode($list);
        return $json;
    }

}