<?php
namespace Yangyao\Mongo\Odm;
use Yangyao\Mongo\Exception;
use Yangyao\Mongo\Db\Api;
//TODO '目前事务的几大问题：*** 现在的查找，是找不到当前进程新建并save了的实例的，因为该实例写到db要等进程结束后才实际执行'
/*
 * '规则说明：
 * *** Transaction还管理了CMD
 * ***只有save和destroy两种动作。
 * ***同一个meta的同一个ID，会记录一个事务序列。
 * ***一旦执行了一次destroy，会加入destroyPool，里面的ID将不能被collection find到。之后如果再次getInstance，会重新new一个初始化meta的实例。再save的话会把此ID从destroy池去掉。
 * ***到最后run的时候，只执行每个ID的事务序列最后的一个动作，比如save-destroy-save,到最后只会执行一次save'
 */

class Transaction {

    const TP_SAVE = 'save';
    const TP_DESTROY = 'destroy';

    private static $transPool = [];

    private static $destroyPool = [];

    private static $odmClassMap = [];

    private static $discarded = false;

    public static function discard(){
        self::$discarded = true;
    }

    public static function discarded(){
        return self::$discarded;
    }

    public static function run() {
        if (!self::discarded()) {
            //dd(self::$transPool);
            foreach(self::$transPool as $schema => $metaArr) {
                foreach($metaArr as $id => $transArr) {
                    $finalTrans = array_pop($transArr);
                    switch ($finalTrans) {
                        case self::TP_SAVE:
                            $className = self::$odmClassMap[$schema];
                            $instance = $className::getInstance($id);
                            $instance->realSave();
                            break;
                        case self::TP_DESTROY:
                            Api::getInstance($schema)->destroy($id);
                            break;
                        default:
                            throw new Exception\System(__METHOD__, 'no such transaction type:'.$finalTrans);
                            break;
                    }
                }
            }
        }
    }

    private static function initPoolItem($schema, $id) {
        !isset(self::$transPool[$schema]) && self::$transPool[$schema] = [];
        !isset(self::$transPool[$schema][$id]) && self::$transPool[$schema][$id] = [];
    }

    public static function addSave($instance){
        $schema = $instance::$schema;
        $id = $instance->getId();
        if (Transaction::toDestroy($schema, $id)) {
            throw new Exception\System(__METHOD__, 'do not save a destroying odm.');
        }

        self::initPoolItem($schema, $id);
        self::$transPool[$schema][$id][] = self::TP_SAVE;
        self::$odmClassMap[$schema] = get_class($instance);
    }

    public static function addDestroy($schema, $id) {
        self::initPoolItem($schema, $id);
        self::$transPool[$schema][$id][] = self::TP_DESTROY;

        self::$destroyPool[$schema][$id] = $id;
    }

    /*
     * NOTICE:这个只是影响和$destroyPool相关的功能。和$transPool无关。
     */
    public static function unsetDestroyPool($schema, $id) {
        unset(self::$destroyPool[$schema][$id]);
    }

    public static function toDestroy($schema, $id) {
        if (isset(self::$destroyPool[$schema]) && isset(self::$destroyPool[$schema][$id])) {
            return true;
        }
        return false;
    }

    public static function destroyIds($schema) {
        if (isset(self::$destroyPool[$schema])) {
            return self::$destroyPool[$schema];
        }
        return [];
    }
}