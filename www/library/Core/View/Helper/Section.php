<?php 

class Core_View_Helper_Section extends Zend_View_Helper_Abstract {

	const DEFAULT_LABEL = 'Section';

	public $data;

	protected $_fc;
	protected $_controller;
	protected $_action;
	protected $_nav;
	protected $_mvc;

	public function __construct() {
		$this->data = new stdClass;

		if( ! is_object($this->_fc) ) {
			$this->_fc = Zend_Controller_Front::getInstance();
		}

		$this->_controller 	= $this->_fc->getRequest()->getControllerName();
		$this->_action 		= $this->_fc->getRequest()->getActionName();

		if( ! is_object($this->_nav) ) {
			$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/subnav.xml', 'subnav');
			$this->_nav = new Zend_Navigation($config);
		}

		//Core_P::p($this->_controller, 0);Core_P::p($this->_action, 0);Core_P::p($this->_nav->toArray(), 1);

		if( ! is_object($this->_mvc) ) {
			$this->_mvc = new Zend_Navigation_Page_Mvc($this->_nav);
		}

		$this->_user = Zend_Auth::getInstance()->getIdentity();
	}
	// END

	/**
	 *	Section
	 *
	 *	@use: 		$this->section();
	 *	@context:	applications/views/scripts/
	 */
	public function section($flag = false) {

		$items = array();
		foreach($this->_nav->getPages() as $page) {

			$resource = $page->resource;

			if( count($ex = explode('|', $resource)) > 0 ) {
				$flag = (in_array($this->_controller, $ex)) ? true:$flag;
			} else {
				$flag = ( $resource == $this->_controller ) ? true:$flag;
			}

			if( $flag ) {

				$pages = array('label' => ucwords($page->label)); 

				if( $page->getPages() ) {

					$pages['links'] = array();
					foreach($page->getPages() as $key => $link) {
						$pages['links'][$key] = array(
															'controller' 	=> $link->controller,
															'action' 		=> $link->action,
															'resource' 		=> $link->resource,
															'label' 		=> ucwords($link->label),
															'active' 		=> $link->active,
															'href' 			=> $this->_getHref($link)
														);

						if( $link->getPages() ) {
							$sublinks = array(); 

							foreach($link->getPages() as $sublink) {
								$sublinks[] = array(
																		'controller' 	=> $sublink->controller,
																		'action' 		=> $sublink->action,
																		'resource' 		=> $sublink->resource,
																		'label' 		=> ucwords($sublink->label),
																		'active' 		=> $sublink->active,
																		'href' 			=> $this->_getHref($sublink)
																	);
							}
							if( count($sublinks) > 0 ) {
								$pages['links'][$key]['sublinks'] = $sublinks;
							}
						}
					}
				}
				if( count($pages['links']) > 0 ) {
					$items[] = $pages;
				}
			}
		}

		if( count($items) > 0 ) {
			$this->data->items = Core_Helper::to_obj($items, false);
		}

		return $this->data;
	}
	// END

	/**
	 *	Section
	 *
	 *	@aspect: 	private
	 *	$param:		object
	 *	$return:	string
	 */
	private function _getHref($link = null) {

		$this->_mvc
			->setController($link->controller)
			->setAction($link->action);

		return $this->_mvc->getHref();
	}
	// END

}
// END CLASS