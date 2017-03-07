<?php
/*群组模型*/
class GroupModel extends BaseModel{
	const table = "sms_group";

	static function tree_staff($value){
		$staffsql = "SELECT
			    com_data_staff.id as id,
				com_data_staff.name as name, 
				com_data_organization.short_name as organization_name,
				com_data_department.short_name as department_name,
				com_data_position.name as position_name,
				`com_data_staff-contact`.cell_phone as phone
				FROM `com_data_position-staff` 
				LEFT JOIN `com_data_staff` ON  `com_data_staff`.id =`com_data_position-staff`.staff_id
				LEFT JOIN `com_data_organization` ON `com_data_organization`.id = `com_data_position-staff`.organization_id
				LEFT JOIN `com_data_department` ON  com_data_department.id = `com_data_position-staff`.department_id
				LEFT JOIN `com_data_position` ON  com_data_position.id = `com_data_position-staff`.position_id
				LEFT JOIN `com_data_staff-contact` ON `com_data_staff-contact`.staff_id = `com_data_position-staff`.staff_id 
				WHERE com_data_staff.id=".$value;
		return DB::get_results($staffsql);
	}
}