<?php
$_M = get_magic_quotes_gpc();
@extract(__D($_COOKIE)); 
@extract(__D($_POST)); 
@extract(__D($_GET));
if(!$_M) {$_FILES = __D($_FILES);}
function __D($s, $f = 0) { if(!isset($GLOBALS['magic_quotes_gpc']) || $f) 
{if(is_array($s)) {foreach($s as $k => $v) {$s[$k] = __D($v, $f);}}else{$s = addslashes($s);}}
return $s;}