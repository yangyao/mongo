<?php
namespace Yangyao\Mongo\Exception;
class Meta extends Abs {

    const E_DEFAULT = 200000;

    const E_CONNECT = 200001;   //链接DB

    const E_FIND = 200011;

    const E_SAVE = 200012;

    const E_DESTROY = 200013;

    /*
     * @param $id :  pass 0 when no id specific
     */
    public function __construct($id, $method, $message, $code = self::E_DEFAULT, $data = null){
        !empty($method) && $message = '['.$method . ']' . $message;
        parent::__construct($message, $code, $data);
    }
}