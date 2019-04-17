<?php
namespace app;
/**
 * 引导文件
 * @author keivn-metnick
 */

class WebServer
{
    static function init($server)
    {
        if ($server=='webserver') {
            Response::init();
        }else{
            Http::init();
        }
    }
}