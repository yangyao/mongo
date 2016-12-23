<?php
namespace Yangyao\Mongo\Db;
use Yangyao\Mongo\Exception;
use Memcached;
class Memcache {

    public static $instance = [];

    public $schema = '';

    public $provider;

    public static function directConnect($schema, $id) {
        $router = Provider::getInstance($schema);
        $connParams = $router->getMemcacheParams($id);
        return Connector\Memcache::connect($connParams['host'], $connParams['port']);
    }

    public static function getInstance($schema) {
        $key = 'Memcache|'.$schema;
        if (!isset(self::$instance[$key])) {
            self::$instance[$key] = New self($schema);
        }
        return self::$instance[$key];
    }

    private function __construct($schema){
        $this->schema = $schema;
        $this->provider = Provider::getInstance($schema);
    }

    public function getKey($id) {
        return $this->schema . '|' . $id;
    }

    public function find($id) {
        $meta = null;
        $memcache = $this->connect($id);
        $data = $memcache->get($this->getKey($id));
        if(is_array($data)){
            $meta = Loader::meta($this->schema);
            $meta->fromArray($data);
            $meta->setId($id);
        }elseif($memcache->getResultCode() != Memcached::RES_NOTFOUND){
            throw new Exception\Meta($id, __METHOD__, $this->schema . '|' . $memcache->getResultMessage(), Exception\Meta::E_FIND);
        }

        return $meta;
    }

    public function save($meta) {
        $id = $meta->getId();
        if (!$id) {
            throw new Exception\Meta(0, __METHOD__, $this->schema . '| no id in meta');
        }
        $memcache = $this->connect($id);

        $memcache->set($this->getKey($id), $meta->toArray('memcache'));
        if ($memcache->getResultCode() != Memcached::RES_SUCCESS) {
            throw new Exception\Meta($id, __METHOD__, $this->schema . '|' . $memcache->getResultMessage(), Exception\Meta::E_SAVE);
        }
    }

    public function destroy($id) {
        $memcache = $this->connect($id);
        $result = $memcache->delete($this->getKey($id));
        if (!$result && $memcache->getResultCode() != Memcached::RES_NOTFOUND) {
            throw new Exception\Meta($id, __METHOD__, $this->schema . '|' . $memcache->getResultMessage(), Exception\Meta::E_DESTROY);
        }
    }

    private function connect($id) {
        $connParams = $this->provider->getMemcacheParams($id);
        return Connector\Memcache::connect($connParams['host'], $connParams['port']);
    }
}