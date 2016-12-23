<?php
namespace Yangyao\Mongo\Db\Connector;
use Yangyao\Mongo\Exception;
class Redis {

    private static $instance = array();

    private static function key($host, $port) {
        return md5($host . ':' . $port);
    }

    public static function connect($host, $port){
        $key = self::key($host, $port);
        if (!isset(self::$instance[$key])) {
            $redis = new \Redis;
            $ret = $redis->pconnect($host, $port);
            if(!$ret) {
                throw new Exception\Meta(0, __METHOD__, "connect to redis failed, host={$host}, port={$port}", Exception\Meta::E_CONNECT);
            }
            self::$instance[$key] = $redis;
        }
        return self::$instance[$key];
    }

    public static function close($host, $port){
        $key = self::key($host, $port);
        try{
            self::$instance[$key]->close();
            unset(self::$instance[$key]);
        }catch(\Exception $e){}
    }

    public static function closeAll() {
        foreach(self::$instance as $k=>$v) {
            try {
                self::$instance[$k]->close();
            }catch(\Exception $e) {}
            unset(self::$instance[$k]);
        }
    }
}