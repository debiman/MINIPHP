<?php
/*
 * MiniPHP.org
 * Coder.Jay 
 * email: i@coderjay.com
 */
namespace Mini;

require_once "plugins.php";
require_once "functions.php";
require_once "core.config.php";

class PHP{
	public static function router(){
			$sp = explode("/",explode("?",(strpos($_SERVER['REQUEST_URI'],"index.php")?$_SERVER['PATH_INFO']:$_SERVER['REQUEST_URI']))[0].'//');
			//var_dump($sp);
			//die;
			$sp[1] = strtolower($sp[1]?:"def");
			$sp[2] = strtolower($sp[2]?:"home");
			$path =  PATH_APP.$sp[1].H.$sp[2].".php";
			if (!file_exists($path)){
				include_once(PATH_TEMPLATES.'public/404.html'); 
				exit();
			}
			include_once $path;
			$class = "\\".$sp[1]."\\".$sp[2];
			if (!class_exists($class)){
				exit("未找到控制器".$class.'！'); 
			}
			$sp[3] = strtolower($sp[3]?:"index");

			if (!in_array($sp[3],get_class_methods($class))){
				exit("未找到方法".$class.'！'); 
			}
			$run = new $class($sp[1],$sp[2],$sp[3],sizeof($sp)>4?array_slice($sp,4,sizeof($sp)-6):array());
			if(in_array('init',get_class_methods($class))){
				$run->init(); 
			}
			$reslt = $run->$sp[3]();
			if(is_array($reslt)){
				exit(json_encode($reslt));
			}else{
				exit($reslt);
			}
	}
	public static function Load($class){
	    $paths = array(PATH_BASE,PATH_PLUGINS,APP_CLASS);
	    for($i=0;$i<count($paths);$i++){
	        $file = $paths[$i].$class.".php";
		    is_file($file) && $i=99999 && include_once($file);
	    }
	}
};

