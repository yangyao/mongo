<?php

use Acme\Entry\News\Entry;

require __DIR__ . "/bootstrap.php";

$id = mongoUniq();//'"585cbda9bd2fc8943f000035"'
$newsEntry = Entry::getInstance($id);
dd($newsEntry->getTitle());
