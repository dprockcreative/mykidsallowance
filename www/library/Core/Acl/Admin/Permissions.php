<?php 

class Core_Acl_Admin_Permissions {

	public function getPermissions($resource = '') {

		switch($resource) {
			case 'auth':
				$permissions = array(
										'users',
										'user'
									);
			break;
		}

		return $permissions;
	}

}
// END CLASS