<?php
//是否打印调试信息
date_default_timezone_set("PRC");
define("APP_DEBUG",true);
if (APP_DEBUG){
	ini_set('display_errors',1);
}

require_once "db.config.php";
define("H",(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? "\\" : "/");
//路径配置信息
define("WEBROOT",realpath(dirname(__FILE__).H."..".H));
define("MINIPHP_PATH", __DIR__); 
define("PATH_BASE",__DIR__.H."base".H); 
define("PATH_RESOURCE","".H."resource".H);	//-------------------------------------资源目录 
define("UPLOAD_PATH",WEBROOT.H."public".H."upload".H);	//-------------------------上传文件系统
define("PATH_WEBAPPRESOURCE", "".H."webapp".H); 	

//-------------------------Extjs系统
define("PATH_EXTJSRESOURCE", "".H."extjs".H); 	

//------------------------------webapp的资源目录
define("PATH_APP",MINIPHP_PATH.H."..".H."App".H."Http".H);//----------------------------控制器目录
define("PATH_TEMPLATES",MINIPHP_PATH.H."..".H."App".H."Templates".H);	//--------------模板目录
define("PATH_PLUGINS",MINIPHP_PATH.H."plugins".H);//---------------------------插件目录
define("APP_CLASS",MINIPHP_PATH.H."..".H."App".H."Class".H);
define("SESSION",true);
define("PAGE_SIZE",10); //分页每页数量

//COOKIE配置信息
define("COOKIE_ID","MiniPHP_COOKIE_ID");
define("COOKIE_DOMAIN","miniphp.org");
define("COOKIE_TIME",3600);

//验证等级
define("USER_LEVEL",1);
define("ROOT_LEVEL",2);
