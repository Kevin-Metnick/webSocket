<?php
namespace app\WsServer;


use app\classes\model;
use app\model\User;

trait TypePrpccessing
{
    /**
     * 信息处理  -- 公共部分
     * @param string $func 消息类型
     * @param string $is_mysql 是否启用数据保存
     * @return  boolean
     */
    private function Common($func, $is_mysql)
    {
        $data = $this->data;

        if(!User::getId(['id'=>$data['receive']])){
            $this->Connection->send(errorJson('不存在此用户'));
            return false;
        }
        $this->Connection->send(successJson('发送成功'));

        //进行“数据库”记录
        $reply = $this->{$func}($data, $is_mysql);

        $replyJson = json_encode($reply);

        if ($is_mysql){
            $this->mysql($reply);

            $this->Connection -> send($replyJson);
        }

        //判断用户是否登录
        if(in_array($data['receive'],array_keys($this->conUser))) {
            $receive = $this->conUser[$data['receive']];

            $receiveConnection = $this->uidConnection[$receive];

            $res = $receiveConnection -> send($replyJson);

            if ($res) return true;
        }

        return false;

    }

    /**
     * 文本处理  转发处理 type=1
     * @param array $data
     * @return array
     */
    private function text($data)
    {
        if(!empty($data['content']) && TypeExists::isUrl($data['content']))
            $data['type']=5;

        $data = $this->returnArr($data);

        return $data;
    }

    /**
     * 图片消息  转发处理 type=2
     * @param array $data
     * @return array
     */
    private function image($data)
    {
        if (!empty($this->data['base64'])) $this->imageBase64();

        $data = $this->returnArr($data);

        return $data;
    }

    /**
     * 文件处理 type=3
     * @param array $data
     * @return array;
     */
    private function file($data)
    {
        if (!empty($this->data['file'])) $this->imageBase64();

        $data = $this->returnArr($data);

        return $data;
    }

    /**
     * 语音   type= 4
     * @param array $data
     * @return array;
     */
    private function voice($data)
    {
        $data = $this->returnArr($data);

        return $data;
    }

    /**
     * 视频 type = 6
     * @param array $data
     * @return array;
     */
    private function video($data)
    {
        $data = $this->returnArr($data);


        return $data;
    }


    /**
     * 动作行为-撤回 type = 10
     * @param array $data
     * @return array;
     */
    private function operation($data)
    {
        $data = $this->returnArr($data);

        return $data;
    }

   /**
     * 动作行为-对方正在输入 type = 14
     * @param array $data
     * @return array;
     */
    private function activities($data)
    {
        if (empty($data['status'])) {
            $data['status'] = 0;
        }
        $data = $this->returnArr($data);

        return $data;
    }

    /**
     * 动作行为-上线提示/下线提示 type = 11
     * @param array $data
     * @param bool $is_open
     * @return array;
     */
    private function notice($data, $is_open=false)
    {
        $receive = @$this->conUser[$data['receive']];
        if(!empty($receive)){
            $receiveConnection = $this->uidConnection[$receive];
            $data['send']=$this->Connection->id;
            $data['is_open'] = $is_open?1:0;
            $data['time']=date('Y-m-d H:i');
            $receiveConnection->send(json_encode($data));
        }
        return $data;
    }



    /**
     *返回数据 格式化
     * @param array $data
     * @return boolean
     */
    private function returnArr($data)
    {
        $array = [
            'send' => $this->Connection->id,
            'time'=>date('Y-m-d H:i'),
        ];

        if (empty($data['message_id'])) {
            $array["message_id"] = "jkt".time().randCid();
            $data['readStatus'] = 1;
        } else {
            $data['readStatus'] = 2;
        }

        return array_merge($array, $data);
    }

    /**
     * 数据写入操作
     * @param array $data
     * @return boolean
     */
    private function mysql($data)
    {
        if (empty($this->data['message_id'])) {
            $resute = model::init()->insert('ws_message_log')->cols([
                'message_id'=> $data['message_id'],
                'type'      => $data['type'],
                'content'   => $data['content'],
                'send'      => $data['send'],
                'receive'   => $data['receive'],
                'readStatus'=> $data['readStatus'],
                'send_time' => $data['time'],
                'up_time'   => date('Y-m-d H:i:s'),
            ])->query();
        } else {
            $resute = model::init()->update('ws_message_log')->cols([
                'readStatus'=> $data['readStatus'],
                'up_time'   => date('Y-m-d H:i:s'),
            ])->where('message_id="'.$data['message_id'].'"')->query();
        }
        return $resute;
    }


    /**
     *图片消息 base64 转发处理
     *@return boolean;
     */
    private function imageBase64()
    {
        return true;
    }
}