<?php
namespace Yangyao\Mongo\Db;
use Yangyao\Mongo\Exception;
class Provider {

    public static $instance = [];

    private $sourceName = '';

    public static function getInstance($schema) {
        $key = 'Router|'.$schema;
        if (!isset(self::$instance[$key])) {
            self::$instance[$key] = New self($schema);
        }
        return self::$instance[$key];
    }

    private function __construct($schema){
        $this->sourceName = Loader::provider($schema);
    }

    public function toCache() {
        $sourceName = $this->sourceName;
        if (!empty($sourceName::$memcache)) {
            return true;
        }
        return false;
    }

    public function toPersist() {
        $sourceName = $this->sourceName;
        if (!empty($sourceName::$mongo)) {
            return true;
        }
        return false;
    }

    public function toQueue() {
        $sourceName = $this->sourceName;
        if (!empty($sourceName::$queue)) {
            return true;
        }
        return false;
    }

    /*
     * @return array('host'=>, 'port'=>, )
     */
    public function getMemcacheParams($id) {
        $sourceName = $this->sourceName;
        $key = $sourceName::getMemcacheKey($id);
        if (isset($sourceName::$memcache[$key])) {
            return $sourceName::$memcache[$key];
        }
        return false;
    }

    /*
 * @return array('host'=>, 'port'=>, 'user'=>, 'pwd'=>, 'db'=>, 'collection'=>, 'w'=>INT, 'j'=>INT, )
 */
    public function getMongoParams($id) {
        $sourceName = $this->sourceName;
        $key = $sourceName::getMongoKey($id);
        if (isset($sourceName::$mongo[$key])) {
            return $sourceName::$mongo[$key];
        }
        return false;
    }
}