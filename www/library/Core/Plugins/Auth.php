<?php 

class Core_Plugins_Auth extends Zend_Controller_Plugin_Abstract {

	private $_acl 		= null;
	private $_auth 		= null;
	private $_config 	= null;

	public function __construct(Zend_Acl $acl, Zend_Auth $auth) {
		$this->_acl  = $acl;
		$this->_auth = $auth;

		$this->_config = Zend_Registry::get('config');

	}
	// END

	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		/**
		 *	Site Offline ?
		 */
		if( empty($this->_config->site->default->online) ) {
			$request->setControllerName('index')->setActionName('offline');
		} else {

			$module 	= $request->getModuleName();
			$resource 	= $request->getControllerName();
			$action 	= $request->getActionName();
			$params 	= $request->getQuery();

			$role 		= $this->_auth->getStorage()->read()->role;

			//Core_P::p($role.' :: '.$resource.' :: '.$action, 1);

			Core_Logger::getInstance()->debug(__METHOD__ . "  role:: $role, resource:: $resource, action:: $action");

			if( ! $this->_acl->isAllowed($role, $resource, $action) ) {
				$sd = new stdClass();
				$sd->_module 		= ( empty($module) ) ? 'default':$module;
				$sd->_controller 	= $resource;
				$sd->_action 		= $action;
				$sd->_params 		= $params;
				
				$in = new Zend_Session_Namespace('sd');
				$in->sd = $sd;

				$request->setControllerName('auth')->setActionName('login');
			}
		}

	}
	// END
}
// END CLASS