<?php
/*分页插件*/
class Pager{
	public static $page = 1;
	public static $limit = 20;
	public static function setLimit($number){
		self::$limit = $number;
	}
	public static function _($sql,$page){
		if (!$page){
			$page = self::$page;
		}
		$limit = self::$limit;
		$start = ($page-1)*$limit;
		$sql = $sql." LIMIT {$start},{$limit}";
		return DB::get_results($sql);
	}
	public static function pages($number){
		return ceil($number/self::$limit);
	}
}