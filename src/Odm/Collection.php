<?php
namespace Yangyao\Mongo\Odm;
use Yangyao\Mongo\Exception;
use Yangyao\Mongo\Db\Mongo;
use Yangyao\Mongo\Db\Loader;
class Collection {

    public static $instance = [];

    public $odmClassName;

    public $schema;

    private $mongo;

    public static function getInstance($odmClassName) {
        if (empty($odmClassName)) {
            throw new Exception\System(__METHOD__, "construct failed without odmClassName.");
        }
        if (!isset(static::$instance[$odmClassName])) {
            static::$instance[$odmClassName] = new self($odmClassName);
        }
        return static::$instance[$odmClassName];
    }

    private function __construct($odmClassName) {
        $this->odmClassName = $odmClassName;
        $this->schema = $odmClassName::$schema;
        $this->mongo = Mongo::getInstance($this->schema);
    }

    //TODO 'NOTICE：现在的查找，是找不到当前进程新建并save了的实例的，因为该实例写到db要等进程结束后才实际执行'
    public function find(Array $query, Array $sort=[], $limit=false) {
        $result = [];
        $mongoColl = $this->mongo->connectSingle();
        $odmClassName = $this->odmClassName;
        $excludeIds = Transaction::destroyIds($this->schema); //TODO '使用Base_Db_Helper_Mongo::addExcludes来改变query，而不是现在的查询出来之后再排除'

        if ($this->mongo->router->toQueue()) { //如果这个meta是有缓存队列的话，必须拿着ID重新从缓存里面读取
            $cursor = $mongoColl->find($query, array('_id'));
            if($limit!==false){
                $cursor->limit($limit);
            }
            if (!empty($sort)) $cursor->sort($sort);
            foreach ($cursor as $data) {
                $id = (string)$data['_id'];
                if (isset($excludeIds[$id])) {
                    continue;
                }

                $result[$id] = $odmClassName::getInstance($id);
            }
        }else {
            $cursor = $mongoColl->find($query);
            if($limit!==false){
                $cursor->limit($limit);
            }
            if (!empty($sort)) $cursor->sort($sort);
            foreach ($cursor as $data) {
                $id = (string)$data['_id'];
                if (isset($excludeIds[$id])) {
                    continue;
                }
                
                if ($odmClassName::hasInstance($id)) { //if existed in Instance, must use it
                    $result[$id] = $odmClassName::getInstance($id);
                    continue;
                }

                //{{{ make meta
                unset($data['_id']);
                $meta = Loader::meta($this->schema);
                $meta->fromArray($data);
                $meta->setId($id);
                //}}}

                $result[$id] = $odmClassName::makeInstance($id, $meta);
            }
        }

        return $result;
    }
}