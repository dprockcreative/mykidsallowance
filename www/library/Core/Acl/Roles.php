<?php 

class Core_Acl_Roles {

	const GUESTS = "guest";
	const USERS  = "user";
	const ADMINS = "admin";

	public function getRoles() {
		return array(
						self::GUESTS,
						self::USERS,
						self::ADMINS
					);
	}
	// END

	public function getRoleFromGroupId($group_id) {
		switch($group_id) {
			case 8;
				return self::ADMINS;
			break;
			case 2;
				return self::USERS;
			break;
			default;
				return self::GUESTS;
			break;
		}
	}
	// END

	public function getGroupIdFromRole($role) {
		switch($role) {
			case self::ADMINS;
				return 8;
			break;
			case self::USERS;
				return 2;
			break;
			default;
				return 1;
			break;
		}
	}
	// END

	public function getGroupOptions($_is_admin = false) {
		$options = array(
						2 => ucwords(self::USERS),
						8 => ucwords(self::ADMINS)
					);

		if( $_is_admin ) {
			$options[1] = ucwords(self::GUESTS);
			ksort($options);
		}

		return $options;
	}
	// END

}
// END CLASS