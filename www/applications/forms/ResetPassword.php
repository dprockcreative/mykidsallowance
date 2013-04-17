<?php

/**
 *	ResetPassword Password
 */
class Form_ResetPassword extends Zend_Form {

	protected $_config;
	protected $_d;

	/**
	 *	Initiate
	 */
	public function init() {

		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->setAttrib('id', 'resetpassword_form')->setMethod('post');

		$this->addElement('hash', 'no_csrf_resetpassword', array('ignore' => true, 'salt' => 'unique', 'decorators' => $this->_d->hidden));

		$this->addElement('password', 'password', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'New Password',
															'class' 			=> 'text text-lrg',
															'maxlength' 		=> 16,
															'autocomplete' 		=> 'off',
															'filters' 			=> array(array('Hash')),
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(6, 32, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							),
																						)
													)
						);

		$this->addElement('password', 'confirmpassword', 
												array(
															'ignore' 			=> true,
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Confirm New Password',
															'class' 			=> 'text text-lrg',
															'maxlength' 		=> 16,
															'autocomplete' 		=> 'off',
															'validators' 		=> array(
																						array(
																								'IdenticalField', true, array('password', 'messages' => array('notMatch' => "Passwords do not match."))
																							),
																						array(
																								'StringLength', true, array(6, 16, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
						);

		$this->addElement('submit', 'save', 
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

		$this->addDisplayGroup(array('password', 'confirmpassword'), 'credentialsgrp', array('legend' => 'Credentials'));
		$this->addDisplayGroup(array('save'), 'savegrp');

		$this->credentialsgrp->setDecorators($this->_d->group);
		$this->savegrp->setDecorators($this->_d->savegroup);
	}
	// END
}
// END CLASS

// FILTERS
require_once 'Zend/Filter/Interface.php';
require_once 'Zend/Loader.php';
class Zend_Filter_Hash implements Zend_Filter_Interface {
	public function __construct() {}
	public function filter($value) {
		return (empty($value)) ? '':md5($value);
	}
}
// END FILTERS


/* End of file ResetPassword.php */
/* Location: ./application/forms/ResetPassword.php */