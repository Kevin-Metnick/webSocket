<?php
namespace app\model;

use Exception;
use JPush\Client as JPush;

/**
 * 极光推送
 * @author kevin knmpby@163.com  19-4-17
 */
class JGuang
{
    /** 推送服务 */
    private $client;

    /** 获取配置 */
    function __construct(){
        $config = config('tuisong');
        $app_key = $config['app_key'];
        $master_secret = $config['master_secret'];
        $this->client = new JPush($app_key, $master_secret);
    }

    /**
     * 给ios用户发送消息
     * @param string $userId  用户id
     * @param string  $group 发送群体
     * @return bool | array
     */
    public function push($userId,$group='one')
    {
        $push = $this->client->push();
        $push->setPlatform('all');
        if ($group=='one'){
            $user = User::getId(['id'=>$userId]);
            if (!empty($user['registrationId']))
                $push->addRegistrationId($user['registrationId']);//指定用户 registrationId
            return errorJson('发送失败, 请检查原因');
        }else{
            $push->addAllAudience(); // 给所有用户发布
        }

        $push->setNotificationAlert("你有一则留言消息");
        try
        {
            $res = $push->send();
            return $res;
        }catch (Exception $e){
            $e->getMessage();
        }
        return false;
    }


}