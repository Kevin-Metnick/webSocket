<?php
namespace app\WsServer;

use app\model\MessageLog;
use app\model\User;
use app\model\UserRelation;

/**
 * 数据接收 处理
 */
class Message
{
    use TypePrpccessing;
    /**
     * 当前会话
     * @var object
     */
    private  $Connection;

    /**
     * 数据
     *@var string
     */
    private $data;

    /**
     * 会话列表
     * @var [$object]
     */
    private $uidConnection;

    /**
     * 用户id
     * @var string
     */
    public $id;

    /**
     * 用户会话列表
     * @var [$object]
     */
    private $conUser;

    /**
     * 发送参数处理
     * @param $Connection
     * @param $data
     * @param array $uidConnection
     * @param array $conUser
     * @return boolean
     */
    function __construct($Connection, $data,array $uidConnection,array $conUser)
    {
        $data = json_decode($data, true);

        $this->Connection = $Connection;

        $this->data = $data;

        $this->uidConnection = $uidConnection;

        $this->conUser = $conUser;

        $this->id = $this->getUserId($this->Connection->id);

        return true;
    }

    /**
     * 用户信息验证
     * @param array $data
     * @return array | bool
     */
    static public function loginVerify($data)
    {
        $token = $data['token'];
        $queryRes = user::getId(['token'=>$token]);

        if (empty($queryRes['id'])) return false;
        return $queryRes;
    }

    /**
     * 用户上下线消息提示
     * @param int $id 用户id
     * @param bool $is_open 是否登录
     * @return bool
     */
    public function alertFriend($id, $is_open=true)
    {
        $list = UserRelation::getList(['friend_id'=>$id]);
        foreach ($list as $key=>$value){
            $this->notice(['type'=>11, 'receive'=>$value['user_id']], $is_open);
        }
        return true;
    }

    /**
     * 用户动作-提示
     * @param  $receive
     * @return bool
     */
    public function alertOperation(int $receive)
    {
        $this->activities(['type'=>14, 'receive'=>$receive, 'status'=>1]);
        return true;
    }


    /**
     * 启动项
     * return string
     */
   public function init()
   {
       return $this->dataType($this->data['type']);
   }

   /**
    * 判断发送的消息类型
    * @param string $data
    * @param bool $is_mysql
    * @return boolean
    */
   private function dataType( $data, $is_mysql=true)
   {
       /**
        * 普通文本消息  1
        * 图片         2
        * 文件         3
        *
        * 语音消息    4
        * 单网址      5
        * 视频        6
        *
        * 撤回        10
        * 系统通知     11  notice
        * 输入提示     14
        */
       $typeArray = [
           1 => 'text',
           2 => 'image',
           3 => 'file',
           4 => 'voice',
           5 => 'url',
           6 => 'video',
           10 => 'operation',
       ];
       $araArray = [
           14 => 'activities',
           11 => 'notice',
       ];
       $type = array_keys($typeArray);
       $ara = array_keys($araArray);
       if (in_array($data,$type)){
           return  $this->Common($typeArray[$data], $is_mysql);
       } else if(in_array($data, $ara)) {
           return  $this->Common($araArray[$data], false);
       }

       $this->Connection->send(errorJson('类型错误'));
       return true;
   }

   /**
    * 发送离线时期未读消息消息
    * @param int $id 用户id
    * @return bool
    */
   public function history($id)
   {
        $hisList = MessageLog::getHisList($id);
        foreach ($hisList as $key=>$value)
        {
            $data=[
                'message_id'=>$value['message_id'],
                'type'      =>$value['type'],
                'content'   =>$value['content'],
                'readStatus'=>$value['readStatus'],
                'receive'=>$value['receive'],
                'send'=>$value['send'],
                'send_time' => $value['send_time']
            ];
            $this->Connection->send(json_encode($data));
        }
        return true;
   }

}
