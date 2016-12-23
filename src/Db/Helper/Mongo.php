<?php
namespace Yangyao\Mongo\Db\Helper;
use Yangyao\Mongo\Exception;
class Mongo {

    public static function addExcludes(& $query, $field, Array $values) {
        $oldValues = [];
        !isset($query[$field]) && $query[$field] = [];
        isset($query[$field]['$nin']) && $oldValues = $query[$field]['$nin'];
        $query[$field]['$nin'] = array_unique(array_merge($oldValues, $values));
    }
}