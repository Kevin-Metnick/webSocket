<?php
namespace app;

/**
 * 调起webwocket
 * @author kevin knmpby@163.com 19-7-17
 */
class Response
{
    static public function init(){

        $wsOperation = new Operation();

        /** 进程数 */
        $wsOperation->count();

        /** 开始操作 */
        $wsOperation->connect();

        /** 消息会话 */
        $wsOperation->message();

        /** 定时任务 */
        $wsOperation->WorkerStart();

        /** 结束操作 */
        $wsOperation->close();

        /** 启动服务 */
        $wsOperation->run();
    }

}