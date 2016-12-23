<?php
namespace Yangyao\Mongo\Db\Connector;
use Yangyao\Mongo\Exception;
use Memcached;
class Memcache {

    private static $instance = array();

    private static function key($host, $port) {
        return md5($host . ':' . $port);
    }

    public static function connect($host, $port){
        $key = self::key($host, $port);
        if (!isset(self::$instance[$key])) {
            $memcache = new Memcached($host . $port);
            if(count($memcache->getServerList()) == 0){
                $ret = $memcache->addServer($host, $port);
                if (!$ret) {
                    throw new Exception\Meta(0, __METHOD__, "connect to memcache failed, host={$host}, port={$port}", Exception\Meta::E_CONNECT);
                }
                $memcache->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
                $memcache->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
                $memcache->setOption(Memcached::OPT_TCP_NODELAY, true);
            }
            self::$instance[$key] = $memcache;
        }

        return self::$instance[$key];
    }

    public static function close($host, $port){
        //no single close api?
        $key = self::key($host, $port);
        unset(self::$instance[$key]);
    }

    public static function closeAll() {
        foreach(self::$instance as $k=>$v) {
            unset(self::$instance[$k]);
        }
    }
}