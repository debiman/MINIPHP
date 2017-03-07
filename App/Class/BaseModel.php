<?php
/**
 * 数据模型基类
 * dreamzk
 * 2016-12-08
 */
class BaseModel{
	public static $page  = 1;//当前页码
	public static $limit = 2;//每页数量

	/*查询总页数*/
	static function gettotal($number,$limit=null){
		if(!$limit){
			$limit = self::$limit;
		}
		return ceil($number/$limit);
	}
	/**
	 *@function 分页查询
	 *@param 查询条件
	 *@param 查询页数
	 *@param 每页数量
	 */
	static function lists($where = NULL,$cloum = "*",$page=null,$limit=NULL){
		if (!$page){
			$page = self::$page;
		}
		if(!$limit){
			$limit = self::$limit;
		}
		if(!$where){
			$where="1=1";
		}
		$start = ($page-1)*$limit;
		$sql = "select {$cloum} from `".static::table."` where {$where} LIMIT {$start},{$limit}";
		return DB::get_results($sql);
	}

	/*查询*/
	static function find($where=NULL,$colum = "*"){

		if(!$where){
			$where="1=1";
		}
		$sql = "select {$colum} from `".static::table."` where {$where}";
		return DB::get_results($sql);
	}

	/*新增*/
	static function add($data){
		if(!is_array($data)){
			exit('传入的参数必须为数组！');
		}
		$sql = "INSERT INTO `".static::table."`(";
		$valsql = " VALUES (";
		foreach ($data as $key => $value) {
			$sql = $sql."`".$key.'`,';
			$valsql = $valsql."'".$value."',";

		}
		$sql = substr($sql,0,strlen($sql)-1);
		$sql = $sql.")";

		$valsql = substr($valsql,0,strlen($valsql)-1);
		$valsql = $valsql .")";

		$sql = $sql.$valsql;

		return DB::query($sql);
	}

	/**
	 *@function 编辑,如果不传参数则条件为根据id更新
	 */
	static function update($data,$condition=null){
		if(!is_array($data)){
			exit('传入的参数必须为数组！');
		}
		$sql = "UPDATE `".static::table."` SET ";
		foreach ($data as $key => $value) {
			$sql = $sql."`".$key."` = '".$value."',";
		}
		$sql = substr($sql,0,strlen($sql)-1);
		if(!$condition){
			$condition ="`id`=".$data['id'];
		}
		$sql = $sql." WHERE ".$condition;
		return DB::query($sql);
	}	

	/*删除*/
	static function delete($id){
		$sql = "delete from `".static::table."` where id = {$id}";
		return DB::query($sql);
	}

	/*计数*/
	static function count($where=null){
		if(!$where){
			$where="1=1";
		}
		$sql = "select count(*) as coun from `".static::table."` where {$where}";
		$res =  DB::get_results($sql);
		return (int)$res[0]->coun;
	}

	static function relation($select=null,$where=null){
		if(!$where){
			$where = "1=1";
		}
		$leftsql = "";
		$relation= explode('|',static::relation);
		foreach ($relation as $key => $value) {
			$_arr = explode(',',$value);
			$lefttable = $_arr[1];
			$foreigkey = $_arr[2];
			if($_arr[0]=='belongsto'){
				$leftsql = $leftsql." inner join {$lefttable} on {$lefttable}.id = ".static::table.".{$foreigkey}";
			}elseif($_arr[0]=='hasmany'){
				$leftsql = $leftsql." inner join {$lefttable} on {$lefttable}.{$foreigkey} = ".static::table.".id";
			}
		}

		if(!$select){
			$cloum = "*";
		}else{
			$cloum = null;
			foreach ($select as $key => $value) {
				$cloum = $cloum.$value.",";
			}
			$cloum = substr($cloum,0,strlen($cloum)-1);
		}
		$sql = "select {$cloum} from ".static::table.
				" {$leftsql}
				where {$where}";
		return DB::get_results($sql);
	}
}