<?php
namespace Yangyao\Mongo\Db\Connector;
use Yangyao\Mongo\Exception;
use MongoClient;
class Mongo {

    private static $instance = array();

    private static function key($host, $port, $user) {
        return md5($host . $port . $user);
    }

    public static function connect($host, $port, $user, $pwd){
        $key = self::key($host, $port, $user);
        if (!isset(self::$instance[$key])) {
            try {
                $destination = "mongodb://{$user}:{$pwd}@{$host}:{$port}";
                /* todo: support replicaSet
                $replecaSet = Config::get('database.mongo')['replicaSet'];
                if (!empty($replecaSet)) {
                    $client = new MongoClient($destination, ['replicaSet'=>$replecaSet]);
                }else {
                    $client = new MongoClient($destination);
                }
                */
                $client = new MongoClient($destination);

                $client->setWriteConcern(1, 30000);
                self::$instance[$key] = $client;
            }catch(\Exception $e) {
                throw new Exception\Meta(0, __METHOD__, "connect to mongo failed, host={$host}, port={$port}|".get_class($e).'|'.$e->getMessage(), Exception\Meta::E_CONNECT);
            }
        }

        return self::$instance[$key];
    }

    public static function close($host, $port, $user){
        //The MongoClient::close() method forcefully closes a connection to the database, even if persistent connections are being used. You should never have to do this under normal circumstances.
        $key = self::key($host, $port, $user);
        unset(self::$instance[$key]);
    }

    public static function closeAll() {
        foreach(self::$instance as $k=>$v) {
            unset(self::$instance[$k]);
        }
    }
}