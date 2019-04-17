<?php
namespace app;

use Workerman\WebServer;
use Workerman\Worker;

class Http
{
    static public function init(){
        $webserver = new WebServer('http://0.0.0.0:80');
        $webserver->addRoot('activate.navicat.com', 'E:\\project\first\\');
//        $webserver->addRoot('blog.example.com', '/your/path/of/blog/');
        $webserver->count = 4;
        Worker::runAll();
    }
}