<?php 

class Core_Acl extends Zend_Acl {

	public function __construct() {

		/**
		 *	RESOURCES
		 */
		$resources = Core_Acl_Resources::getResources();
		foreach($resources as $resource) {
			$this->add(new Zend_Acl_Resource($resource));
		}

		/**
		 *	ROLES
		 */
		$roles = Core_Acl_Roles::getRoles();
		$inher = '';
		foreach($roles as $i => $role) {
			if($i > 0) {
				$this->addRole(new Zend_Acl_Role($role), $inher);
			} else {
				$this->addRole(new Zend_Acl_Role($role));
			}
			$inher = $role;
		}

		/**
		 *	GUEST
		 */
		$this->allow('guest', Core_Acl_Guest_Permissions::getPermissions());
		$this->allow('guest', 'auth', Core_Acl_Guest_Permissions::getPermissions('auth'));

		/**
		 *	USER
		 */
		$this->allow('user', 'auth', Core_Acl_User_Permissions::getPermissions('auth'));
		$this->allow('user', 'account', Core_Acl_User_Permissions::getPermissions('account'));
		$this->allow('user', 'allowances', Core_Acl_User_Permissions::getPermissions('allowances'));
		$this->allow('user', 'configurations', Core_Acl_User_Permissions::getPermissions('configurations'));
		$this->deny('user', 'auth', array('login', 'signup'));

		/**
		 *	ADMIN
		 */
		$this->allow('admin', 'auth', Core_Acl_Admin_Permissions::getPermissions('auth'));
		$this->allow('admin', 'admin'); 
	}
	// END

}
// END CLASS