<?php
/**
 * @auth dreamzk
 * @time 2016-11-22
 * @version 1.0
 * @email dream_zk2014@sohu.com
 */
//获取客户端IP
function _IP() {
	if (getenv ('HTTP_CLIENT_IP') && strcasecmp ( getenv ('HTTP_CLIENT_IP'), 'unknown' ))
		return getenv ( 'HTTP_CLIENT_IP' );
	else if (getenv ('HTTP_X_FORWARDED_FOR') && strcasecmp ( getenv ('HTTP_X_FORWARDED_FOR'), 'unknown'))
		return  getenv ('HTTP_X_FORWARDED_FOR');
	else if (getenv ('REMOTE_ADDR') && strcasecmp ( getenv ('REMOTE_ADDR'), 'unknown'))
		return  getenv ('REMOTE_ADDR');
	else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], 'unknown'))
		return  $_SERVER ['REMOTE_ADDR'];
	return 'unknown';
}
//包装原生$_GET，没有设置也会得到一个null值
function _GET($n = null) {
	return $n&&isset($_GET[$n])?$_GET[$n]:null;
}
//包装原生$_POST，没有设置也会得到一个null值
function _POST($n = null) {
	return $n&&isset($_POST[$n])?$_POST[$n]:null;
}
//改装原生basename使其支持中文
function _basename($f){
	return preg_replace('/^.+[\\\\\\/]/','',$f);
}

//检测文件上传
function _ExistsFile($res = false){
	return sizeof($_FILES)?$_FILES:$res;
}

//检测当时是否存在POST请求
function _ISPOST($res = false){
	return ($_SERVER['REQUEST_METHOD']=="POST")?$_POST:$res;
}

//检测当前请求是否存在GET请求
function _ISGET($res = false){
	return ($_SERVER['REQUEST_METHOD']=="GET")?$_GET:$res;
}

/** 
 * @description 根据时间戳创建文件夹/年/月/日/分/秒
 * @param       创建目录的父目录
 */
function _CreateTimeDir($basedir = UPLOAD_PATH){
	$hour = date('H',time());
	$min = date('i',time());
	$sec = date('s',time());

	$yeardir = $basedir.date('Y',time());
	$monthdir = $yeardir.'/'.date('m',time());
	$daydir = $monthdir.'/'.date('d',time());
	$timedir = $daydir.'/'.$hour.$min.$sec;
	if(!is_dir($timedir)){
		$mkres = mkdir($timedir.'/',0777,true);
		if(!$mkres) return false;
	}else{
		return $timedir;
	}
	return $timedir;
}

//移动file到配置目录
function _MOVEFILEUOLOAD(){
	foreach($_FILES as $fk=>$fv){
		//按照时间戳建立目录
		$creatdir = _CreateTimeDir();
		if($creatdir){
			move_uploaded_file($_FILES[$fk]["tmp_name"],$creatdir.'/'.$fv["name"]);
			//存储到文件数据库
			$id = time().str_pad(mt_rand(1,99999),5,'0',STR_PAD_LEFT);
			$istemp = 1;
			$operation=2;
			$apps=substr($_SERVER["QUERY_STRING"],4);
			$created_at = date('Y-m-d H:i:s',time());
			$fileSQL = "INSERT INTO itfree_files (id,type,size,unit,path,filename,istemp,operation,apps,created_at) 
						VALUES($id,'".$fv['type']."','".$fv['size']."','kb','".$creatdir."','".$fv['name']."','".$istemp."','".$operation."','".$apps."','".$created_at."')";
			$sqlres = DB::query($fileSQL);
			if($sqlres){
				array_push($res,$id);
			}else{
				exit($fv['name'].'存数据库失败！');
			}
		}else{
			exit("创建目录失败！");
		}
	}
	return $res;
}

function _Redirect($url="/home/index"){
	header("Location:".$url);
	exit;
}

/*返回json字符串*/
function _ReturnAjax($data){
	header( 'Access-Control-Allow-Origin:*' );
	header( 'Access-Control-Allow-Credentials:true' );
	echo json_encode($data);
	exit();
}

/**
 *阿里大于短信发送
 */
function _SendMsgSMS($senddata){
	LoadPlugins::alidy();
	$c = new TopClient;
	$Parm = explode(',',$senddata['smsParam']);
	$translate_param = '{';

	for($i=0;$i<sizeof($Parm);$i++){
		$translate_param .= "s".($i+1).":'".$Parm[$i]."',";
	}
	$translate_param = trim($translate_param,',')."}";
	$c ->appkey = $senddata['appkey'] ;
	$c ->secretKey = $senddata['secret'] ;
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	$req ->setExtend( "" );
	$req ->setSmsType( "normal" );
	$req ->setSmsFreeSignName($senddata['autograph']);  //签名
	$req ->setSmsParam( $translate_param );  //发送内容
	$req ->setRecNum( $senddata['phone'] );				//发送内容
	$req ->setSmsTemplateCode(  $senddata['smscode'] );				//应用id
	$resp = $c ->execute( $req );
	$result = json_decode(json_encode($resp),TRUE);
	return $result;
}

/*php非form封装post请求*/
function _MiniPOST($url,$postdata){
		if(is_array($postdata)){
			$postdata = json_encode($postdata);
		}
		$ch = curl_init ();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0 );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/json',
		    'Content-Length: ' . strlen($postdata))
		);
		$return = curl_exec($ch);
		curl_close($ch);  
		return $return;
	}
