<?php
class mHttp{ 
	public $namespace = "";
	public $arguments = array();
	public $class = "";
	public $function = "";
	function __construct($namespace,$class,$function,$arguments){
		if(SESSION){
			session_start(); 
		}
		$this->namespace = $namespace;
		$this->class = $class;
		$this->function = $function;
		$this->arguments = $arguments;
	}

	function index(){
		$this->load();
	}
	function load($namespace=null,$function = null,$class = null){
		$class = $class?:$this->class;
		$function = $function?:$this->function;
		$namespace = $namespace?:$this->namespace;
        $templates=PATH_TEMPLATES.$namespace."/".$class."/".$function.".php";
		if (file_exists($templates)){
			include_once $templates;
		}else{
			throw new Exception("file not exists", 1);
		}
		exit();
	}
} 