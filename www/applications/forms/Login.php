<?php

/**
 *	LOG IN
 */
class Form_Login extends Zend_Form {

	protected $_config;
	protected $_d;

	/**
	 *	Initiate
	 */
	public function init() {

		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->setAttrib('id', 'login_form')->setMethod('post');

		$this->addElement('hash', 'no_csrf_login', array('ignore' => true, 'salt' => 'unique', 'decorators' => $this->_d->hidden));

		$this->addElement('hidden', '_login', array('ignore' => true, 'value' => 1, 'decorators' => $this->_d->hidden));

		$this->addElement('text', 'username', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'User Name',
															'class' 			=> 'text text-lrg',
															'maxlength' 		=> 64,
															'validators' 		=> array(
																						array(
																								'StringLength', false, array(6, 16, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
						);

		$this->addElement('password', 'password', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Password',
															'class' 			=> 'text text-lrg',
															'maxlength' 		=> 16,
															'validators' 		=> array(
																						array(
																								'StringLength', false, array(6, 16, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
						);

		$this->addElement('submit', 'login', 
												array(
															'ignore' 			=> true,
															'decorators' 		=> $this->_d->hidden,
															'label' 			=> 'Log In',
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

		$this->addDisplayGroup(array('username', 'password'), 'logingrp');
		$this->addDisplayGroup(array('login'), 'savegrp');

		$this->logingrp->setDecorators($this->_d->simple_group);
		$this->savegrp->setDecorators($this->_d->savegroup);
	}
	// END

}
// END CLASS


/* End of file Login.php */
/* Location: ./application/forms/Login.php */