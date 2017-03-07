<?php
/*
 * Coder.Jay
 * MiniPHP v 0.1
 */

//防mysql注入
header("Content-Type: text/html; charset=utf-8");
require_once "../MiniPHP/shield.php";

require_once "../MiniPHP/main.php";
use \Mini as Mini;
spl_autoload_register(array('Mini\PHP', 'Load')); // -- autoload
Mini\PHP::router();