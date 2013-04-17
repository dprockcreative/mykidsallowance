<?php 

class IndexController extends Zend_Controller_Action {

	public $data;
	public $config;

	/**
	 *	Init
	 */
	public function init() {
		$this->_helper->layout->setLayout('layout');	
		$this->data 			= new stdClass;
		$this->data->config 	= Zend_Registry::get('config');

		$this->_helper->getHelper('FlashMessenger');

		$this->view->headTitle()->prepend('Home');
	}
	// END

	/**
	 *	FM - Dispatch
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function preDispatch() {
		$this->data->fms = $this->_helper->_FlashMessenger->getMessages();
	}
	// END

	/**
	 *	ACTION = Index
	 */
	public function indexAction() {
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = Offline
	 */
	public function offlineAction() {
		$this->_helper->layout->setLayout('simple');
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = Account
	 *
	 * 	@Proxies to Account Controller, which inherits Auth & ACL
	 */
	public function accountAction() {
		$this->_helper->layout->disableLayout();
		$this->_forward('index', 'account', null, array('data' => $this->data));
	}
	// END

	/**
	 *	ACTION = Flash Message Display
	 */
	public function fmdAction() {
		$this->_helper->layout->disableLayout();
		if( count($this->data->fms) == 0) {
			exit("0");
		}
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = Update
	 */
	public function updateAction() {

		$table 		= (string) $this->_getParam('table');
		$field 		= (string) $this->_getParam('field');
		$value 		= (string) $this->_getParam('value');
		$key 		= (int) $this->_getParam('key');

		if( empty($table) || empty($field) || empty($value) || empty($key) ) {
			exit("-1");
		}

		$query = Doctrine::getTable($table)->find($key);

		if( empty($query) ) {
			exit("-2");			
		}

		if( ! isset($query->$field) ) {
			exit("-3");
		}

		$query->{$field} = $value;

		$query->save();

		if( empty($query->id)  ) {
			exit("-4");
		}

		exit("1"); 
	}
	// END

	/**
	 *	ACTION = Delete
	 */
	public function deleteAction() {

		$table 	= (string) $this->_getParam('table');
		$key 	= (int) $this->_getParam('key');

		if( empty($table) || empty($key) ) {
			exit("-1");
		}

		$query = Doctrine::getTable($table)->find($key);

		if( empty($query) ) {
			exit("-2");			
		}

		if( ! $query->delete() ) {
			exit("-4");
		}

		exit("1"); 
	}
	// END

}
// END CLASS


// EOF