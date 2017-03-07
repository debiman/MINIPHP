<?php
/*发送模型*/
class SendModel extends BaseModel{
	const table="sms_send";
	const relation = "belongsto,sms_user,user_id|hasmany,sms_send_items,send_id";
}