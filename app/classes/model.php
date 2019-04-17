<?php
namespace app\classes;

use Workerman\MySQL\Connection;

/**
 * model类
 * return object
 */
class model
{
    /**
     * 储存model
     * @var object
     */
    static private $Connection;
    /**
     * 实例化model类
     */
    static public function init()
    {
        if (empty(self::$Connection) ||!(self::$Connection instanceof Connection)) {
            $config = config('mysql');
            $conObj = new Connection(
                $config['host'], $config['port'], $config['user'], $config['pass'],$config['db_name']
            );
            self::$Connection = $conObj;
        }
        return self::$Connection;
    }


}