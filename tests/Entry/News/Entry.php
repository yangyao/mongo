<?php
/**
 * Created by PhpStorm.
 * User: yangyao
 * Date: 2016/12/22
 * Time: 19:19
 */

namespace Acme\Entry\News;
use Yangyao\Mongo\Odm\Abs;


class Entry extends  Abs{

    public static $schema = 'news.entry';
    public static $instance = [];

    public static function create($title,$content){
        $id = mongoUniq();
        $entry = self::getInstance($id);
        $entry->setTitle($title);
        $entry->setContent($content);
        $entry->save();
        return $entry;
    }
}