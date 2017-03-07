<?php
class DB{
	static private $db = null;
	public static function __callStatic($name,$args){
		if (!self::$db){
			//self::$db = new ezSQL_postgresql(DB_USER,DB_PASS,DB_NAME);
			self::$db = new ezSQL_mysql(DB_USER,DB_PASS,DB_NAME,DB_HOST,'utf8');
			//self::$db->query("SET names utf8");
		}
		return call_user_func_array(array(self::$db,$name), $args);
	}
}

class COOKIE{
	static private $cookie = null;
	public static function __callStatic($name,$args){
		if (!self::$cookie){
			self::$cookie = new mCookie();
		}
		return call_user_func_array(array(self::$cookie,$name), $args);
	}
}

class LoadPlugins{
	public static function alidy(){
		require_once(PATH_PLUGINS.'alidy/TopSdk.php');
	}
}
