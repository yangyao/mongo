<?php
namespace Yangyao\Mongo\Db;
use Yangyao\Mongo\Exception;
class Mongo {

    public static $instance = [];

    public $schema = '';

    public $provider;

    public static function getInstance($schema) {
        $key = 'Mongo|'.$schema;
        if (!isset(self::$instance[$key])) {
            self::$instance[$key] = New self($schema);
        }
        return self::$instance[$key];
    }

    private function __construct($schema){
        $this->schema = $schema;
        $this->provider = Provider::getInstance($schema);
    }

    public function find($id) {
        $meta = null;
        try {
            $collection = $this->connect($id);
            $data = $collection->findOne(array('_id' => $id));
            if(is_array($data)){
                unset($data['_id']);
                $meta = Loader::meta($this->schema);
                $meta->fromArray($data);
                $meta->setId($id);
            }
        }catch(\Exception $e) {
            throw new Exception\Meta($id, __METHOD__, $this->schema.'|'.get_class($e).'|'.$e->getMessage(), Exception\Meta::E_FIND);
        }

        return $meta;
    }

    public function save($meta) {
        $id = $meta->getId();
        if (!$id) {
            throw new Exception\Meta(0, __METHOD__, $this->schema . '|no id in meta');
        }
        try {
            $collection = $this->connect($id);

            $wOptions = []; //default is w=1, j=0.   Critical data, like Orders, must set w=1 and j=1(true).   Insignificant data, like logs, should set w=0, j=false
            $configs = $this->provider->getMongoParams($id);
            isset($configs['w']) && $wOptions['w'] = (int)$configs['w'];
            isset($configs['j']) && $wOptions['j'] = boolval($configs['j']);
            $collection->save(array_merge(array('_id'=>$id), $meta->toArray('mongo')), $wOptions);
        }catch(\Exception $e) {
            //if w=1 ,then write failure can be caught by exception; otherwise, we don't care the safety while w=0
            throw new Exception\Meta($id, __METHOD__, $this->schema.'|'.get_class($e).'|'.$e->getMessage(), Exception\Meta::E_SAVE);
        }
    }

    public function destroy($id) {
        try {
            $collection = $this->connect($id);

            $wOptions = [];
            $configs = $this->provider->getMongoParams($id);
            isset($configs['w']) && $wOptions['w'] = (int)$configs['w'];
            isset($configs['j']) && $wOptions['j'] = boolval($configs['j']);
            $collection->remove(array('_id' => $id), $wOptions);
        }catch(\Exception $e) {
            throw new Exception\Meta($id, __METHOD__, $this->schema.'|'.get_class($e).'|'.$e->getMessage(), Exception\Meta::E_DESTROY);
        }
    }

    /*
     * @return MongoCollection
     */
    private function connect($id) {
        $connParams = $this->provider->getMongoParams($id);
        $conn = Connector\Mongo::connect($connParams['host'], $connParams['port'], $connParams['user'], $connParams['pwd']);
        return $conn->selectCollection($connParams['db'], $connParams['collection']);
    }

    /*
     * mongo 事实上只应该一个meta对应一个collection，不使用分库分表，如果要分布式就用sharding。这样才可以使用查询语句。
     */
    public function connectSingle() {
        return $this->connect(0);
    }
}