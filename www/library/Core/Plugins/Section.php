<?php 

class Core_Plugins_Section extends Zend_Controller_Plugin_Abstract {

	protected $data;

	public function __construct() {}

	public function preDispatch() {
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/subnav.xml', 'subnav');
		$nav 	= new Zend_Navigation($config);
		$view 	= new Zend_View();
	}
	// END


}
// END CLASS