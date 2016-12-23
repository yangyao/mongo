<?php

use Acme\Entry\News\Entry;

require __DIR__ . "/bootstrap.php";
use Yangyao\Mongo\Odm\Transaction;
$id = mongoUniq();
$entry = Entry::getInstance($id);
$entry->setTitle('hello world');
$entry->setContent("welcome to mongodb");
try{
    $entry->save();
    Transaction::run();
}catch (Exception $e){
    Transaction::discard();
}



