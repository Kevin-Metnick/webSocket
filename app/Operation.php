<?php
namespace app;

use app\model\user;
use app\WsServer\Message;
use Workerman\Lib\Timer;
use Workerman\worker;

/**
 * Operation  websocket 操作类
 *
 * @author kevin knmpby@163.com 19-4-17
 */
class Operation
{
    /**
     * WebSocker 服务
     * @var object
     */
    private $ws_servr;

    /**
     * 服务端请求
     * @var string
     */
    private $url = "websocket://0.0.0.0:9999";

    /**
     *  进程
     * @var int
     */
    private $count = 4;

    /**
     * 记录已验证的连接情况
     * @var [object]
     */
    private $uidConnection;

    /**
     * 记录未验证的连接
     * @var [object]
     */
//    private $unVerifyConnection;

    /**
     * 用户id - 连接id 关联
     * @var array
     */
    private $conUser=[];


    /**
     *  实例化 websocket服务
     */
    function __construct()
    {
        Worker::$logFile = './tmp/log/server.log';
        $this->ws_servr = new worker($this->url);
    }

    /**
     * webscoket服务 进程配置
     *
     */
    function count(){
        $this->ws_servr->count = $this->count;
    }

    /**
     *websocket服务 连接配置
     */
    public function connect()
    {
        $this->ws_servr->onConnect = function ($connection){
              $this->uidConnection[$connection->id] = $connection;
            echo "连接成功\n";
        };
    }

    /**
     * websocket服务 数据接收配置
     */
    public function message()
    {
        $this->ws_servr->onMessage = function ($connection, $data) {
            if (!in_array($connection->id,array_values($this->conUser))){
                $dataArr = json_decode($data, true);
                if (!empty($dataArr['token'])){
                    $verify = Message::loginVerify($dataArr);
                } else {
                    $verify = false;
                }

                if ($verify) {
                    User::setLogin(['id'=>$verify['id']],true);
                    $this->conUser[$verify['id']] = $connection->id;
                    $connection->send(successJson("验证成功"));
                    $messsage = new Message($connection, $data, $this->uidConnection,$this->conUser);

                    $messsage->history($verify['id'],$connection);    //发送历史消息
                    $messsage->alertFriend($verify['id']);
                    return true;
                }else{
                    $connection->send(errorJson("请进行登录验证"));
                    return false;
                }
            }

            $messsage = new Message($connection, $data, $this->uidConnection,$this->conUser);
            $messsage->init();
            echo "发送消息";

        };
    }


    /**
     *websocket服务 设置推出操作
     */
    public function close()
    {
        $this->ws_servr->onClose = function($connection)
        {
            $id = $connection->id;
            foreach ($this->conUser as $key=>$value) {
                if ($value==$id){
                    //下线提示
                    $messsage = new Message($connection, '', $this->uidConnection,$this->conUser);
                    $messsage->alertFriend($key,false);
                    echo "用户".$id;
                    unset($this->conUser[$key]);
                    user::setLogin(['id'=>$key],0);
                }
            }
            unset($this->uidConnection[$id]);
            echo " 关闭连接\n";
        };
    }

    /**
     *设置定时任务
     *
     */
    public function WorkerStart()
    {
        $this->ws_servr->onWorkerStart = function ($worker){
            Timer::add(1, function()use($worker){
                $time_now = time();
                if (isset($GLOBALS['Timer'])){
                    foreach ($GLOBALS['Timer'] as $key=>$value) {
                        if ($time_now - $value > 10){
                            $idArray = explode('-', $key);
                            $sendId = $idArray['0'];
                            $receive = $idArray['1'];
                            $data = [
                                'type'=>14,
                                'receive'=> $receive,
                                'status'=>1
                            ];
                            $connection = $this->uidConnection[$this->conUser[$sendId]];
                            $message = new Message($connection,json_encode($data),$this->uidConnection,$this->conUser);
//                            $message->alertOperation($receive)
                            $message->init();
                        }
                    }
                }

                foreach($worker->connections as $connection) {
                    // 有可能该connection还没收到过消息，则lastMessageTime设置为当前时间
                    if (empty($connection->lastMessageTime)) {
                        $connection->lastMessageTime = $time_now;
                        continue;
                    }
                    // 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
                    if ($time_now - $connection->lastMessageTime > HEARTBEAT_TIME) {
                        $connection->close();
                    }
                }
            });
        };
    }

    /**
     *设置错误提示
     */
    public function error()
    {
        $this->ws_servr->onError = function ($connection, $code, $msg)
        {
            echo sprintf("error :  %i  %i \n", $code,$msg);
        };
    }

    /**
     * 启动workerman  调起websocket
     *
     * @throws Workerman\worker
     */
    public function run()
    {
        worker::runAll();
    }
}