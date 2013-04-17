<?php 

class TemplatesController extends Zend_Controller_Action {

	public $data;
	public $config;

	/**
	 *	Init
	 */
	public function init() {
		$this->_helper->layout->setLayout('layout');	
		$this->data 			= new stdClass;
		$this->data->config 	= Zend_Registry::get('config');
		$this->_helper->layout->disableLayout();
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
	 *	ACTION = Internal
	 *
	 * 	@Used for Modal window display
	 */
	public function internalAction() {

		/**
		 * 	Detect Ajax Request
		 */
		if( ! isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
			$this->_redirect('/');
		}

		/**
		 * 	Request
		 */
		$request 	= $this->getRequest()->getRequestUri();
		$params 	= explode('/', $request);
		$query 		= array();
		foreach($params as $row) {
			if( ! empty($row) ) {
				$query[] = $row;
			}
		}

		//Core_P::p($query, 1);
		/**
		 * 	Extract Template File
		 */
		$dir 	= $query[0];
		$query 	= array_slice($query, 1);
		$templ 	= implode('.', $query).'.tpl';
		$path 	= $dir.'/'.$templ;

		/**
		 * 	Validate and Return
		 */
		if( file_exists(APPLICATION_PATH.'/views/scripts/'.$path) ) {
			$output = $this->view->assign((array) $this->data)->render($path);
			exit($output);
		} else {
			exit("0");
		}
	}
	// END

}
// END CLASS


// EOF