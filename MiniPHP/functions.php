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


/*短信异常处理*/
function _SmsError($errorcode){
	switch ($errorcode)
	{
		case 'isv.OUT_OF_SERVICE':
		 	$result['message']='业务停机！';
		  	break; 
		case 'isv.PRODUCT_UNSUBSCRIBE':
		 	$result['message']='产品服务未开通！';
		  	break; 
		case 'isv.ACCOUNT_NOT_EXISTS':
		 	$result['message']='账户信息不存在！';
		  	break; 
		case 'isv.ACCOUNT_ABNORMAL':
		 	$result['message']='账户信息异常！';
		  	break; 
		case 'isv.SMS_TEMPLATE_ILLEGAL':
		 	$result['message']='模板不合法！';
		  	break; 
		case 'isv.SMS_SIGNATURE_ILLEGAL':
		 	$result['message']='签名不合法！';
		  	break;  
		case 'isv.MOBILE_NUMBER_ILLEGAL':
		 	$result['message']='手机号码格式错误！';
		  	break;  
		case 'isv.MOBILE_COUNT_OVER_LIMIT':
		 	$result['message']='手机号码数量超过限制【手机号码以英文逗号分隔，不超过200个号码！】！';
		  	break;  
		case 'isv.TEMPLATE_MISSING_PARAMETERS':
		 	$result['message']='短信模板变量缺少参数！';
		  	break;  
		case 'isv.INVALID_PARAMETERS':
		 	$result['message']='参数异常！';
		  	break;  
		case 'isv.BUSINESS_LIMIT_CONTROL':
		 	$result['message']='触发业务流控限制【短信通知，使用同一签名、同一模板，对同一手机号发送短信通知，允许每天50条（自然日）】！';
		  	break; 
		case 'isv.INVALID_JSON_PARAM':
		 	$result['message']='JSON参数不合法！';
		  	break;  
		case 'isv.SYSTEM_ERROR':
		 	$result['message']='运营商系统错误！';
		  	break;  
		case 'isv.BLACK_KEY_CONTROL_LIMIT':
		 	$result['message']='模板变量中存在敏感词汇！';
		  	break;  
		case 'isv.PARAM_NOT_SUPPORT_URL':
		 	$result['message']='不支持url为变量！';
		  	break; 
		case 'isv.PARAM_LENGTH_LIMIT':
		 	$result['message']='变量长度受限！';
		  	break; 
		case 'isv.AMOUNT_NOT_ENOUGH':
		 	$result['message']='余额不足，请联系管理员尽快充值！';
		  	break;  
		default:
		 	$result['message']='未知错误，请联系系统管理员或开发人员！'; 	
	}
	return $result;
}


//人员排序函数封装 
function _PersinSort($staff_id,$person_array){
        $staff_id = $staff_id?:$staff_id;
        $sort_arr = $person_array?:$data['sortArr'];
        $sss =  
        //得到已经排序好的staff_id数组===============================================
        $division_id_arr = $PositionStaffRelation->orderBy('division_id', 'ASC')->lists('division_id');
        $division_id_arr = array_unique($division_id_arr);
        //得到排好序的机构id数组
        $all_org_arr = array();
        foreach($division_id_arr as $division_id_arrK=>$division_id_arrV){
            $mold_id_arr = $Organization->select(array('com_data_organization-mold.id as mold_id'))->leftjoin('com_data_organization-mold', 'com_data_organization.mold_id', '=', 'com_data_organization-mold.id')
                ->where('com_data_organization.division_id', '=', $division_id_arrV)
                ->orderBy('com_data_organization-mold.sort', 'ASC')
                ->lists('mold_id');
            $mold_id_arr = array_unique($mold_id_arr);
            $organization_id_arr = array();
            foreach($mold_id_arr as $mold_id_arrK=>$mold_id_arrV){
                $mold_org_arr = $Organization->where('division_id', '=', $division_id_arrV)->where('mold_id', '=', $mold_id_arrV)->where('connected', '=', 1)->orderBy('sort')->lists('id');
                $organization_id_arr = array_merge($organization_id_arr, $mold_org_arr);
            }
            $all_org_arr = array_merge($all_org_arr, $organization_id_arr);
        }
        $staff_sort_id_arr = array();
        foreach($all_org_arr as $all_org_arrK=>$all_org_arrV){
            $top_dep_id_arr = $Department->where('organization_id', '=', $all_org_arrV)->where('level_id', '=', 0)->orderBy('sort', 'ASC')->lists('id');//一级部门
            $dep_id_arr = array();
            foreach($top_dep_id_arr as $top_dep_id_arrK=>$top_dep_id_arrV){
                array_push($dep_id_arr, $top_dep_id_arrV);
                $secondary_dep_id_arr = $Department->where('organization_id', '=', $all_org_arrV)->where('level_id', '=', $top_dep_id_arrV)->orderBy('sort', 'ASC')->lists('id');//二级部门
                $dep_id_arr = array_merge($dep_id_arr, $secondary_dep_id_arr);
            }
            foreach($dep_id_arr as $dep_id_arrK=>$dep_id_arrV){
                $dep_staff_arr = $PositionStaffRelation->where('department_id', '=', $dep_id_arrV)->orderBy('sort', 'ASC')->lists('staff_id');
                foreach($dep_staff_arr as $dep_staff_arrK=>$dep_staff_arrV){
                    $exist = in_array($dep_staff_arrV, $staff_sort_id_arr);//判断是否重复
                    if(!$exist){
                        array_push($staff_sort_id_arr, $dep_staff_arrV);
                    }
                }
            }
        }
        //对人员进行排序===============================================
        $result = array();
        foreach($staff_sort_id_arr as $staff_sort_id_arrK=>$staff_sort_id_arrV){
            foreach($sort_arr as $sort_arrK=>$sort_arrV){
                if($staff_sort_id_arrV == $sort_arrV[$staff_id]){
                    array_push($result, $sort_arrV);
                }
            }
        }
        //检测是否有其他机构的人员===============================================
        if(sizeof($result) != sizeof($sort_arr)){
            foreach($sort_arr as $sort_arrK=>$sort_arrV){
                if(!in_array($sort_arrV[$staff_id], $staff_sort_id_arr)){
                    array_push($result, $sort_arrV);
                }
            }
        }
        return $result;
}