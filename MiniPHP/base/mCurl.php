<?php

class mCurl{
	static function get($url,$sourceCode="gb2312"){
		$ch = curl_init();  
		$this_header = array(
			"content-type: application/x-www-form-urlencoded; charset=$sourceCode"
		);

		curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);
		curl_setopt($ch, CURLOPT_URL, $url);  
		//curl_setopt($ch, CURLOPT_HEADER, 0);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );  
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);  
		$res = curl_exec($ch);  
		curl_close($ch);
		//mb_convert_encoding($res, "UTF-8", "GBK,gb2312,UTF-8");
		return $res;
	}
}
?>