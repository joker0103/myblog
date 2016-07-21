<?php 
namespace app\model;
 class UserModel extends \core\Model
 {
 	public static $table = "user";
 	public function getPowerName($users)
 	{	
 		if (is_array($users)) {
 			foreach ($users as $key => $value) {
	 			switch ($value->power) {
	 				case 1:
	 					$value->powerName = '大站长';
	 					break;
	 				case 2:
	 					$value->powerName = '管理员';
	 					break;
	 				default:
	 					$value->powerName = '小博主';
	 					break;
	 			}
 			}
 		}else {
 			switch ($users->power) {
	 				case 1:
	 					$users->powerName = '大站长';
	 					break;
	 				case 2:
	 					$users->powerName = '管理员';
	 					break;
	 				default:
	 					$users->powerName = '小博主';
	 					break;
	 		}
 		}	
 		return $users;
 	}
 }
