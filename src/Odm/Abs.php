<?php
namespace Yangyao\Mongo\Odm;
use Yangyao\Mongo\Db\Api;
use Yangyao\Mongo\Db\Loader;
use Yangyao\Mongo\Exception;
abstract class Abs {

    //{{{ should be defined by child
    //public static $schema = '';

    //public static $instance = [];

    //meta fields that cannot be accessed using getXxx() or setXxx()
    //NOTICE '数组类型字段都自动设置为private,不需要指定'
    protected $privateFields = [];
    //}}}

    protected $id;

    protected $meta;

    protected $isNew = false;

    protected $saved = false;

    protected $privateFunctions = [];

    public static function getInstance($id) {
        if (empty($id)) {
            throw new Exception\System(__METHOD__, "instance need id.");
        }
        $class = get_called_class();
        if (!isset(static::$instance[$id])) {
            static::$instance[$id] = new $class($id);
        }
        return static::$instance[$id];
    }

    public static function hasInstance($id) {
        return isset(static::$instance[$id]);
    }

    public static function makeInstance($id, $meta) {
        if (empty($id) || !is_object($meta)) {
            throw new Exception\System(__METHOD__, "makeInstance failed with wrong params.");
        }
        $class = get_called_class();
        static::$instance[$id] = new $class($id, $meta);
        return static::$instance[$id];
    }

    /*
     * $id可以是对象instance
     */
    public static function destroyInstance($id) {
        is_object($id) && $id = $id->getId();
        if(static::hasInstance($id)) {
            $target = static::getInstance($id);
            $target->beforeDestroy();
            $target = null;
            unset($target);
            unset(static::$instance[$id]);
        }
        Transaction::addDestroy(static::$schema, $id);
    }

    public static function lists(Array $ids) {
        $list = array();
        foreach($ids as $_id) {
            $list[$_id] = static::getInstance($_id);
        }
        return $list;
    }

    /*
     * Note: find is slow!
     */
    public static function findList(Array $query, Array $sort=[], $limit=false) {
        return static::collection()->find($query, $sort, $limit);
    }

    /*
     * Note: find is slow!
     */
    public static function findOne(Array $query) {
        $list = static::collection()->find($query);
        if (!empty($list)) {
            return array_shift($list);
        }
        return null;
    }

    public static function collection() {
        return Collection::getInstance(get_called_class());
    }

    final private function __construct($id, $meta=null) {
        if(!property_exists(get_called_class(), 'schema') || !property_exists(get_called_class(), 'instance')) {
            throw new Exception\System(__METHOD__, 'properties lack');
        }
        if (!is_string($id)) {
            throw new Exception\System(__METHOD__, "id must be string");
        }

        $this->id = $id;
        if (is_object($meta)) {
            /*if (get_class($meta) != static::$schema) {
                throw new Exception\System(__METHOD__, 'construct with wrong meta.');
            }*/
            $this->meta = $meta;
        }elseif (Transaction::toDestroy(static::$schema, $id)) {    //'如果是在待删除池里面，则重新建立一个空的实例，相当于允许同一个ID 删除->新建 这样的事务流'
            $this->meta = null;
            Transaction::unsetDestroyPool(static::$schema, $id);
        }else {
            $dbApi = Api::getInstance(static::$schema);
            $this->meta = $dbApi->find($id);
        }

        if(!$this->meta){
            $this->isNew = true;
            $this->meta = Loader::meta(static::$schema);
            $this->meta->setId($id);
        }else{
            $this->isNew = false;
        }

        $this->markFunctionsPrivate();
        $this->init();
    }

    private function markFunctionsPrivate() {
        //mark for private fields
        $privates = [];
        foreach($this->meta->fields  as $_name => $_type) {
            $_type == 'ARRAY' && $privates[] = $_name;
        }
        foreach(array_merge($privates, $this->privateFields) as $_f) {
            $_uf = ucfirst($_f);
            $this->privateFunctions['set' . $_uf] = true;
            $this->privateFunctions['get' . $_uf] = true;
        }
    }

    protected function init() {
    }

    final public function globalInstanceId() {
        return get_class($this).'|'.$this->getId();
    }

    public function getId(){
        return $this->id;
    }

    final public function isExists(){
        return !$this->isNew && $this->id!=null;
    }

    final public function isNew(){
        return !$this->isExists();
    }

    final public function isSaved(){
        return $this->saved;
    }

    protected function beforeSave(){}

    protected function afterSave(){}

    public function beforeDestroy() {}

    final protected  function updateTime() {
        if($this->isNew()){
            $this->setCt(TIME_NOW);
        }
        $this->setMt(TIME_NOW);
    }

    final public function save() {
        $this->updateTime();
        Transaction::addSave($this);
        $this->isNew = false;
    }

    final public function realSave() {
        $this->updateTime();
        $this->setV($this->getV()+1);
        $this->beforeSave();
        $dbApi = Api::getInstance(static::$schema);
        $dbApi->save($this->meta);
        $this->isNew = false;
        $this->saved = true;
        $this->afterSave();
    }

    public function toArray(){
        $arr = $this->meta->toArray();
        $arr['id'] = $this->getId();
        return $arr;
    }

    public function toLog() {
        Base_Log_Action::meta(static::$schema, $this->getId(), $this->meta);
    }

    public function __call($func, $args){
        if (isset($this->privateFunctions[$func])) {
            throw new Exception\System(__METHOD__, 'the meta function is private!'.$func);
        }

        $returnVal = call_user_func_array(array($this->meta,$func), $args);
        if ($returnVal === $this->meta) {
            return $this;
        }
        return $returnVal;
    }

    final public function take($field){
        $method = 'get' . ucfirst($field);
        return $this->$method();
    }

    final public function put($field){
        $method = 'set' . ucfirst($field);
        return $this->$method();
    }
}
