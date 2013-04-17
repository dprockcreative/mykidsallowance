<?php 

class Core_Acl_User_Permissions {

	public function getPermissions($resource = '') {

		switch($resource) {
			case 'auth':
			case 'account':
				$permissions = array(
										'account',
										'allowances',
										'index', 
										'profile',
										'logout'
									);
			break;
			case 'allowances':
			case 'configurations':
				$permissions = array(
										'account',
										'allowances',
										'allowance',
										'allowancesettings',
										'allowancesetting',
										'concierge',
										'history',
										'index',
										'logout',
										'print',
										'setting',
										'settings',
										'stub'
									);
			break;
		}

		return $permissions;
	}

}
// END CLASS