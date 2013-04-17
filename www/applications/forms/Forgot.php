<?php

/**
 *	Forgot Password
 */
class Form_Forgot extends Zend_Form {

	protected $_config;
	protected $_d;

	/**
	 *	Initiate
	 */
	public function init() {

		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->setAttrib('id', 'forgot_form')->setMethod('post');

		$this->addElement('hash', 'no_csrf_forgot', array('ignore' => true, 'salt' => 'unique', 'decorators' => $this->_d->hidden));

		$this->addElement('hidden', '_forgot', array('ignore' => true, 'value' => 1, 'decorators' => $this->_d->hidden));

		$this->addElement('text', 'email', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Email Address',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 64,
															'autocomplete' 		=> 'off',
															'validators' 		=> array(
																						array(
																								'EmailAddress', true, array('messages' => $this->_email_messages)
																							),
																						array(
																								'EmailExists', true
																							)
																						)
													)
												);

		$this->addElement('submit', 'reset', 
												array(
															'ignore' 			=> true,
															'decorators' 		=> $this->_d->hidden,
															'label' 			=> 'Reset Password',
															'class' 			=> 'submit'
													)
						);
	}
	// END

	/* ------------------------------ *
	 *	Default Decorators
	 * ------------------------------ */
	public function loadDefaultDecorators() {
		$this->setDecorators($this->_d->default);

		$this->addDisplayGroup(array('email'), 'forgotgrp', array('legend' => 'Forgot Password?', 'description' => "We'll send a link to your address that will allow you to reset your password"));
		$this->addDisplayGroup(array('reset'), 'resetgrp');

		$this->forgotgrp->setDecorators($this->_d->simple_group);
		$this->resetgrp->setDecorators($this->_d->savegroup);
	}
	// END

	protected $_email_messages = array(
										'emailAddressInvalid' 			=> "Invalid type",
										'emailAddressInvalidFormat' 	=> "'%value%' invalid format",
										'emailAddressInvalidHostname' 	=> "'%hostname%' invalid hostname",
										'emailAddressInvalidMxRecord' 	=> "'%hostname%' invalid MX record",
										'emailAddressInvalidSegment' 	=> "'%hostname%' not routable.",
										'emailAddressDotAtom' 			=> "'%localPart%' not matched dot-atom format",
										'emailAddressQuotedString' 		=> "'%localPart%' not matched quoted-string format",
										'emailAddressInvalidLocalPart' 	=> "'%localPart%' invalid local part",
										'emailAddressLengthExceeded' 	=> "'%value%' exceeds allowed length"
									);
}
// END CLASS


// VALIDATORS
require_once 'Zend/Validate/Abstract.php';

class Zend_Validate_EmailExists extends Zend_Validate_Abstract {
	const NOT_EXIST = 'notExist';
	protected $_messageTemplates = array(
		self::NOT_EXIST => "Email not recognized."
	);
	public function __construct() {}
	public function isValid($value) {
		$this->_value = $value;
		try {
			$query = Doctrine::getTable('Members')->findByEmail($this->_value);
			$count = $query->count();
		} catch(Exception $e) {
			Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
		}
		if( empty($count) ) {
			$this->_error(self::NOT_EXIST);
			return false;
		} else {			
			return true;
		}
	}
}
// END VALIDATORS


/* End of file Forgot.php */
/* Location: ./application/forms/Forgot.php */