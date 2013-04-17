<?php

class ErrorController extends Zend_Controller_Action {

	public $data;
	public $config;

	public function init() {
		$this->_helper->layout->setLayout('layout');
	}

	/**
	 *	ERROR ACTION
	 */
	public function errorAction() {

		$errors = $this->_getParam('error_handler');

		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				$this->getResponse()->setHttpResponseCode(404);
			break;
			default:
				$this->getResponse()->setHttpResponseCode(500);
			break;
		}

		$r 		= new Zend_Controller_Response_Http();
		$code 	= $r->getHttpResponseCode();

		$this->view->code 		= $code;
		$this->view->message 	= $errors->exception->getMessage();


		$this->view->data   	= $this->data;
		$this->view->exception 	= $errors->exception;

		$this->view->request   	= $errors->request;

		Core_Logger::getInstance()->err($errors->exception);
	}
	// END

}
// END CLASS