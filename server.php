<?php
/**
 * workerman 实现聊天
 * @version 1.0
 * @author kevin-metnick
 */
require __DIR__.'/Common/functions.php';
require __DIR__.'/vendor/autoload.php';

define('HEARTBEAT_TIME', 55);
/** 实例化web */
app\WebServer::init('webserver');