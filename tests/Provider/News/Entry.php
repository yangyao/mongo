<?php

namespace Acme\Provider\News;


class Entry {
    public static $mongo = array(
        0=>array('host'=>'localhost','port'=>'27017','user'=>'admin','pwd'=>'admin123','db'=>'demo','collection'=>'news_entry', 'w'=>1, 'j'=>0),
    );

    public static $memcache = null;

    public static $queue = null;

    public static function getMongoKey($id) {
        return 0;
    }

    public static function getMemcacheKey($id) {
        return 0;
    }
}