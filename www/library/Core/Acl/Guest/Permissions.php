<?php 

class Core_Acl_Guest_Permissions {

	public function getPermissions($resource = '') {

		switch($resource) {
			case 'auth':
				$permissions = array(
										'index', 
										'confirm', 
										'error', 
										'login', 
										'resend', 
										'reactivate', 
										'resetpassword', 
										'signup'
									);
			break;
			default:
				$permissions = array(
										'index', 
										'confirm', 
										'error', 
										'fmd', 
										'menu',
										'resetpassword',
										'sitemap' 
									);
			break;
		}

		return $permissions;
	}

}
// END CLASS