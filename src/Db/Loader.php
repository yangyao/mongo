<?php
namespace Yangyao\Mongo\Db;
use Yangyao\Mongo\Exception;
class Loader {

    /*
     * @param $schema = 'xxx.aaa.bbb'
     */
    public static function meta($schema) {
        require_once  self::metaFile($schema);
        $className = self::metaClassName($schema);
        return new $className();
    }

    public static function metaClassName($schema) {
        $arr = explode('.', $schema);
        $classShortName = ucfirst(array_pop($arr));
        $namespace = 'Yangyao\\Mongo\\Db\\Meta';
        foreach($arr as $v) {
            $v = ucfirst($v);
            $namespace .= '\\' . $v;
        }
        return $namespace.'\\'.$classShortName;
    }

    public static function metaClassShortName($schema) {
        $arr = explode('.', $schema);
        return  ucfirst(array_pop($arr));
    }

    public static function metaClassNamespace($schema) {
        $arr = explode('.', $schema);
        array_pop($arr);
        $namespace = 'Yangyao\\Mongo\\Db\\Meta';
        foreach($arr as $v) {
            $v = ucfirst($v);
            $namespace .= '\\' . $v;
        }
        return $namespace;
    }

    public static function metaFileFolder() {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Meta';
    }

    public static function schemaFileFolder() {
        if(!defined('ODM_PATH_SCHEMA')){
            throw new Exception\System(__METHOD__, "ODM_PATH_SCHEMA must be defined.");
        }
        return ODM_PATH_SCHEMA;
    }

    public static function metaFile($schema) {
        $arr = explode('.', $schema);
        $path = self::metaFileFolder();
        foreach($arr as $v) {
            $v = ucfirst($v);
            $path .= DIRECTORY_SEPARATOR . $v;
        }
        return $path.'.php';
    }

    public static function metaXml($schema) {
        if(!defined('ODM_PATH_SCHEMA')){
            throw new Exception\System(__METHOD__, "ODM_PATH_SCHEMA must be defined.");
        }
        return ODM_PATH_SCHEMA . $schema. '.xml';
    }

    /*
     * 'TODO 不同环境下，读取不同的provider'
     */
    public static function provider($schema) {
        if(!defined('ODM_PATH_PROVIDER')){
            throw new Exception\System(__METHOD__, "ODM_PATH_PROVIDER must be defined.");
        }
        if(!defined('ODM_PROVIDER_NS')){
            throw new Exception\System(__METHOD__, "ODM_PROVIDER_NS must be defined.");
        }
        $arr = explode('.', $schema);
        $path = ODM_PATH_PROVIDER;
        $className = ODM_PROVIDER_NS;
        foreach($arr as $v) {
            $v = ucfirst($v);
            $className.= '\\'.$v;
            $path .= DIRECTORY_SEPARATOR . $v;
        }
        require_once  $path.'.php';
        return $className;
    }

}