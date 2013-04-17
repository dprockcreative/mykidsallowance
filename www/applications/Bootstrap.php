<?php 

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	private $_config 	= null;
	private $_acl 		= null;
	private $_auth 		= null;
	private $_view 		= null;
	private $_jqv 		= '1.4.4';

	protected function _initConfig() {
		$this->_config = new Zend_Config_Ini($this->getOption('config'), APPLICATION_ENV);
		Zend_Registry::set('config', $this->_config);
	}

	protected function _initAutoload() {
		$ma = new Zend_Application_Module_Autoloader(array('namespace' => '', 'basePath' => dirname(__FILE__)));

		$la = Zend_Loader_Autoloader::getInstance();
		$la->registerNamespace(array('Core_'));

		$this->_acl 	= new Core_Acl;
		$this->_auth 	= Zend_Auth::getInstance();
		if( ! $this->_auth->hasIdentity()) { 
			$this->_auth->getStorage()->read()->role = 'guest';
		}

		$fc = Zend_Controller_Front::getInstance();
		$fc->registerPlugin(new Core_Plugins_Auth($this->_acl, $this->_auth));
		//$fc->registerPlugin(new Core_Plugins_Section());

		return $ma;		  
	}

	function _initNavigation() {
		$this->bootstrap('layout');
		$layout 		= $this->getResource('layout');
		$this->_view 	= $layout->getView();
	
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/nav.xml', 'nav');
		$nav 	= new Zend_Navigation($config);
		$this->_view->navigation($nav)->setAcl($this->_acl)->setRole($this->_auth->getStorage()->read()->role);
	}

	protected function _initDoctrine() {

		require_once 'Doctrine.php';
	
		$db = $this->getOption('database');
		if($db) {
			try {
				$host 		= $db['default']['host'];
				$db_name 	= $db['default']['name'];
				$uname 		= $db['default']['username'];
				$pwd 		= $db['default']['password'];

				$db_url = 'mysql://'.$uname.':'.$pwd.'@'.$host.'/'.$db_name;
				$dsn 	= 'mysql:dbname='.$db_name.';host='.$host;

				//Register Doctrine's own autoloader for loading Doctrine classes and tables
				spl_autoload_register(array('Doctrine', 'autoload'));

				$dbh  = new PDO($dsn, $uname, $pwd, array(PDO::ATTR_PERSISTENT => true));
				$conn = Doctrine_Manager::connection($dbh, 'default');
				$conn->setOption('username', $uname);
				$conn->setOption('password', $pwd);
				//print_r($conn);die;

				//Set the model files to be autoloaded
				$manager = Doctrine_Manager::getInstance();
				$manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
				$manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
				//Now set the path to the models
				Doctrine::loadModels(APPLICATION_PATH . '/models');

				//Doctrine::generateModelsFromDb('models', array('default'), array('detect_relations' => true));
			} catch(Exception $e) {
				print $e;
			}
		} else {
			throw new Exception('config.ini does not have any database configuration');
		}
	}

	protected function _initRouting() {
		$routes = new Zend_Config_Ini($this->getOption('routes'), APPLICATION_ENV);
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$router->addConfig($routes, 'routes');
	}

	protected function _initDoctype() {
		$this->_view->doctype('XHTML5');
		$this->_view->headTitle($this->_config->site->default->name->string)->setSeparator(' :: ');
		$this->_view->headScript()
			->appendFile('http://ajax.googleapis.com/ajax/libs/jquery/'.$this->_jqv.'/jquery.min.js');
	}

	protected function _initHelpers() {
		$this->_view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
		$viewRenderer->setView($this->_view);
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
	}

	protected function _initLogging() {
		Core_Logger::setup($this->_config);
	}

}
// END CLASS


// EOF