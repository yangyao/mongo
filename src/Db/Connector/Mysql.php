<?php
namespace Yangyao\Mongo\Db\Connector;
use Yangyao\Mongo\Exception;
use PDO;
class Mysql {

    private static $instance = array();

    private static function key($host, $port, $user) {
        return md5($host . $port . $user);
    }

    public static function connect($host, $port, $user, $pass){
        $key = self::key($host, $port, $user);
        if (!isset(self::$instance[$key])) {
            try {
                $dsn = 'mysql:host=' . $host . ';port=' . $port;
                $pdo = new PDO($dsn, $user, $pass, array(PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance[$key] = $pdo;
            }catch(\Exception $e) {
                throw new Exception\Meta(0, __METHOD__, "connect to mysql failed, host={$host}, port={$port}|".$e->getMessage(), Exception\Meta::E_CONNECT);
            }
        }

        return self::$instance[$key];
    }

    public static function close($host, $port, $user){
        $key = self::key($host, $port, $user);
        unset(self::$instance[$key]);
    }

    public static function closeAll() {
        foreach(self::$instance as $k=>$v) {
            unset(self::$instance[$k]);
        }
    }
}