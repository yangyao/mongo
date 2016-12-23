<?php
namespace Yangyao\Mongo\Db;
use Yangyao\Mongo\Exception;
class Api {

    public static $instance = [];

    public $schema = '';

    public $provider;

    private $persistence;

    private $cache;


    public static function closeAllConnector() {
        Connector\Memcache::closeAll();
        Connector\Mongo::closeAll();
        Connector\Redis::closeAll();
    }

    public static function getInstance($schema) {
        $key = 'Api|'.$schema;
        if (!isset(self::$instance[$key])) {
            self::$instance[$key] = New self($schema);
        }
        return self::$instance[$key];
    }

    private function __construct($schema){
        if (empty($schema)) {
            throw new Exception\Meta(0, __METHOD__, "construct failed without metaName.");
        }
        $this->schema = $schema;
        $this->provider = Provider::getInstance($schema);
        $this->persistence = Mongo::getInstance($schema);
        $this->cache = Memcache::getInstance($schema);
    }

    public function find($id) {
        $meta = null;

        if ($this->provider->toCache()) {
            $meta = $this->cache->find($id);
            if (is_object($meta)) {
                return $meta;
            }
        }

        if ($this->provider->toPersist()) {
            $meta = $this->persistence->find($id);
        }

        if (is_object($meta)) {
            if ($this->provider->toCache()) {
                $this->cache->save($meta);
            }
            return $meta;
        }

        return false;
    }

    public function save($meta) {
        if ($this->provider->toCache()) {
            $this->cache->save($meta);
        }

        if ($this->provider->toPersist()) {
            $this->persistence->save($meta);
        }
    }

    public function destroy($id) {
        if ($this->provider->toCache()) {
            $this->cache->destroy($id);
        }

        if ($this->provider->toPersist()) {
            $this->persistence->destroy($id);
        }
    }
}