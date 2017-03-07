<?php
/*用户模型*/
class UserModel extends BaseModel{
	const table="sms_user";
	static function login($username,$password){
		$sql = "SELECT 
			 sms_user.username as username,
			 `com_data_staff-contact`.cell_phone as phone,
			 sms_user.name as name,
			 sms_user.role_id as role_id,
			 sms_user.id as id
			 FROM sms_user 
			 INNER JOIN `com_data_staff-contact` ON  sms_user.staff_id = `com_data_staff-contact`.staff_id 
			 WHERE sms_user.username = '".$username."' AND sms_user.password = '".$password."' LIMIT 1";
		return DB::get_results($sql);
	}
	
	/*对当前人员排序*/
	static function staffsort($data){
		$result = [];
		foreach($data as $key=>$value){
			$query_staffinfo_sql = "SELECT com_data_organization.sort as o_sort,
							com_data_department.sort as d_sort,
							com_data_position.sort as p_sort,
							com_data_staff.sort as s_sort
						FROM com_data_staff
						INNRE JOIN `com_data_position-staff` ON `com_data_position-staff`.staff_id = com_data_staff.id
						INNER JOIN `com_data_position` ON `com_data_position-staff`.position_id = com_data_position.id
						INNER JOIN `com_data_department` ON `com_data_department`.id = `com_data_position-staff`.department_id
						INNER JOIN `com_data_organization` ON `com_data_position-staff`.organization_id = com_data_organization.id
						WHERE com_data_staff.id = {$value->staff_id}
						ORDER BY com_data_organization.sort asc
						ORDER BY com_data_department.sort asc
						ORDER BY com_data_position.sort asc
						ORDER BY com_data_staff.sort asc";
			$result[$key]['sorts'] = DB::get_results($query_staffinfo_sql)[0];
			$result[$key]['staff_id'] = $value->staff_id;
		}
		/*机构排序*/
		for($i = 0;$i<sizeof($result);$i++){
			for($j = 0; $j<$i;$j++){
				if($result[$i]['sorts']->o_sort>$result[$j]['sorts']->o_sort){
					$temp = $result[$j];
					$result[$j]= $result[$i];
					$result[$i] = $temp;		
				}
			}
		}
		/*部门排序*/
		for($i = 0;$i<sizeof($result);$i++){
			for($j = 0; $j<$i;$j++){
				if($result[$i]['sorts']->d_sort>$result[$j]['sorts']->d_sort){
					$temp = $result[$j];
					$result[$j]= $result[$i];
					$result[$i] = $temp;		
				}
			}
		}
		/*职务排序*/
		for($i = 0;$i<sizeof($result);$i++){
			for($j = 0; $j<$i;$j++){
				if($result[$i]['sorts']->p_sort>$result[$j]['sorts']->p_sort){
					$temp = $result[$j];
					$result[$j]= $result[$i];
					$result[$i] = $temp;		
				}
			}
		}
		/*人员排序*/
		for($i = 0;$i<sizeof($result);$i++){
			for($j = 0; $j<$i;$j++){
				if($result[$i]['sorts']->s_sort>$result[$j]['sorts']->s_sort){
					$temp = $result[$j];
					$result[$j]= $result[$i];
					$result[$i] = $temp;		
				}
			}
		}
		return $result;
	}
	//人员信息
	static function userposition($uid){
		$sql = "SELECT
			    com_data_staff.id as id,
				com_data_staff.name as name, 
				com_data_organization.short_name as  organization_name,
				com_data_department.short_name  as  department_name,
				com_data_position.name as position_name,
				`com_data_staff-contact`.cell_phone as phone
				FROM `com_data_position-staff` 
				LEFT JOIN `com_data_staff` ON  `com_data_staff`.id =`com_data_position-staff`.staff_id
				LEFT JOIN `com_data_organization` ON `com_data_organization`.id = `com_data_position-staff`.organization_id
				LEFT JOIN `com_data_department` ON  com_data_department.id = `com_data_position-staff`.department_id
				LEFT JOIN `com_data_position` ON  com_data_position.id = `com_data_position-staff`.position_id
				LEFT JOIN `com_data_staff-contact` ON `com_data_staff-contact`.staff_id = `com_data_position-staff`.staff_id WHERE com_data_staff.id = ".$uid;
		$res =  DB::get_results($sql);
		if(!$res){
			return false;
		}
		return self::position_info($res);
	}
	
	static function getinfophone($phone){
		$sql = "SELECT
			    	com_data_staff.id as id,
				com_data_staff.name as name, 
				com_data_organization.short_name as  organization_name,
				com_data_department.short_name  as  department_name,
				com_data_position.name as position_name,
				`com_data_staff-contact`.cell_phone as phone
				FROM `com_data_position-staff` 
				LEFT JOIN `com_data_staff` ON  `com_data_staff`.id =`com_data_position-staff`.staff_id
				LEFT JOIN `com_data_organization` ON `com_data_organization`.id = `com_data_position-staff`.organization_id
				LEFT JOIN `com_data_department` ON  com_data_department.id = `com_data_position-staff`.department_id
				LEFT JOIN `com_data_position` ON  com_data_position.id = `com_data_position-staff`.position_id
				LEFT JOIN `com_data_staff-contact` ON `com_data_staff-contact`.staff_id = `com_data_position-staff`.staff_id WHERE `com_data_staff-contact`.cell_phone = ".$phone;
		$res =  DB::get_results($sql);
		if(!$res){
			return false;
		}
		return self::position_info($res);
	}
	
	//拼装人员职务信息
	static function position_info($res){
		if(!is_array($res)){
			return $res;
		}
		foreach($res as $key=>$value){
			$query_staffdetail_sql = "SELECT com_data_position.name as staffpositionname, 
						com_data_organization.name as stafforganization, 
						com_data_organization.short_name as oshort_name, 
						com_data_department.name as staffdepartment, 
						com_data_department.short_name as short_name, 
						com_data_department.sort as sort, 
						com_data_organization.id as organization_id, 
						com_data_position.id as position_id,
						com_data_position.name as pname
						FROM `com_data_staff`
						LEFT JOIN `com_data_position-staff` ON `com_data_staff`.id = `com_data_position-staff`.staff_id
						LEFT JOIN `com_data_organization` ON `com_data_position-staff`.organization_id = `com_data_organization`.id 
						LEFT JOIN `com_data_department` ON `com_data_position-staff`.department_id = `com_data_department`.id
						 LEFT JOIN `com_data_position` ON `com_data_position-staff`.position_id = `com_data_position`.id 
						WHERE com_data_staff.id = {$value->id}
						ORDER BY `com_data_position-staff`.p_sort asc";
	                $query_staffdetail = DB::get_results($query_staffdetail_sql);
	                $organizationstr=null;
		        $departmentstr=null;
		        $rankstr=null;
		        $org_array = [];
		        $count=count($query_staffdetail);
	            for($i=0;$i<$count;$i++){
	                if($query_staffdetail[$i]->short_name){
	                    $departmentname=$query_staffdetail[$i]->short_name;
	                }else{
	                    $departmentname=$query_staffdetail[$i]->staffdepartment;
	                }
	                $orgname = $query_staffdetail[$i]->oshort_name;
	                $positoinname = $query_staffdetail[$i]->pname;
	                
	 		if($query_staffdetail[$i]->staffdepartment == '委厅领导及人员' || $query_staffdetail[$i]->staffdepartment == '委厅领导' ||$query_staffdetail[$i]->staffdepartment == '委局领导' || $query_staffdetail[$i]->staffdepartment == '正厅级纪检员监察专员' || $query_staffdetail[$i]->staffdepartment == '副秘书长' || $query_staffdetail[$i]->staffdepartment == '副厅级纪检员监察专员' || $query_staffdetail[$i]->staffdepartment == '副厅级纪检员、监察专员' || $query_staffdetail[$i]->staffdepartment == '正厅级纪检员、监察专员'){
                    		$organization_detail=$orgname.$positoinname.'、';
                    	}else{
	                    if($i>0){
	                        if($query_staffdetail[$i]->staffdepartment == $query_staffdetail[$i-1]->staffdepartment){ //如果当前这个部门等于上一个部门则不需要
	                          $organization_detail= $orgname.$positoinname.'、';
	                        }else{
	                            $organization_detail=$orgname.$departmentname.$positoinname.'、';
	                        }
	                    }else{
	                        $organization_detail=$orgname.$departmentname.$positoinname.'、';
	                    }
	                }
	                $organizationstr=$organizationstr.$organization_detail;
	            }
	           $res[$key]->org_dep=substr($organizationstr,0,strlen($organizationstr)-3); //机构以及职务
	         }
		return $res;
	}
	//找到人员关联群组信息
	static function groupinfo($data){
		foreach($data as $key=>$value){
			//找到当前人员的所有群组信息
			$groupstr = "";
			$sql= "SELECT * FROM sms_group where person like '%{$value->id}%'";
			$groupinfo=  DB::get_results($sql);
			if(sizeof($groupinfo)){
				foreach($groupinfo as $keys=>$values){
					if($keys==0){
						$groupstr= $values->name;
					}else{
						$groupstr = $groupstr.'、'.$values->name;
					}
				}
			}
			$data[$key]->group = $groupstr;
			
		}
		return $data;
	}
}