<?php

/**
 *	Profile
 */
class Form_Profile extends Zend_Form {

	protected $_config;
	protected $_user;
	protected $_account;
	protected $_d;

	/**
	 *	Init
	 */
	public function init() {

		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->setAttrib('id', 'profile_form')->setMethod('post');

		$this->addElement('hash', 'no_csrf_profile', array('ignore' => true, 'salt' => 'unique', 'decorators' => $this->_d->hidden));

		$this->addElement('text', 'username', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'User Name',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 16,
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(6, 16, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
						);

		$this->addElement('text', 'screenname', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Screen Name',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 32,
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(3, 32, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
						);

		$this->addElement('password', 'password', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'New Password',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 16,
															'autocomplete' 		=> 'off',
															'filters' 			=> array(array('Hash')),
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(6, 32, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
						);

		$this->addElement('password', 'confirmpassword', 
												array(
															'ignore' 			=> true,
															'required' 			=> false,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Confirm Password',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 16,
															'autocomplete' 		=> 'off',
															'validators' 		=> array(
																						array(
																								'IdenticalField', true, array('password', 'messages' => array('notMatch' => "Passwords do not match."))
																							),
																						array(
																								'StringLength', true, array(6, 32, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
						);

		$this->addElement('select', 'group_id', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Group',
															'class' 			=> 'select select-med',
															'multiOptions' 		=> $this->_multiOptions('group_id')
													)
						);

		$this->addElement('checkbox', 'active', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->cb_element,
															'label' 			=> 'Membership Active',
															'description' 		=> '&nbsp;',
															'unCheckedValue' 	=> null,
															'class' 			=> 'checkbox'
													)
						);

		$this->addElement('password', 'authenticatepassword', 
												array(
															'ignore' 			=> true,
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Current Password',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 16,
															'autocomplete' 		=> 'off',
															'filters' 			=> array(array('Hash'))
													)
						);

		$this->addElement('reset', 'reset', 
												array(
															'ignore' 			=> true,
															'decorators' 		=> $this->_d->hidden,
															'label' 			=> 'Reset',
															'class' 			=> 'submit',
															'tabindex' 			=> 999
													)
						);

		$this->addElement('submit', 'save', 
												array(
															'ignore' 			=> true,
															'decorators' 		=> $this->_d->hidden,
															'label' 			=> 'Save',
															'class' 			=> 'submit'
													)
						);
	}
	// END

	/* ------------------------------ *
	 *	DEFAULT DECORATORS
	 * ------------------------------ */
	public function loadDefaultDecorators() {
		$this->setDecorators($this->_d->default);

		$this->addDisplayGroup(array('username', 'screenname'), 'logingrp', array('legend' => 'Presence'));
		$this->logingrp->setDecorators($this->_d->group);

		$this->addSubForms(array('Members' => new Form_Members()));

		$this->addDisplayGroup(array('password', 'confirmpassword', 'group_id', 'active'), 'admingrp', array('legend' => 'Credentials'));
		$this->admingrp->setDecorators($this->_d->group);

		$this->addDisplayGroup(array('authenticatepassword'), 'authenticategrp', array('legend' => 'Authenticate Changes'));
		$this->authenticategrp->setDecorators($this->_d->group);

		$this->addDisplayGroup(array('reset', 'save'), 'savegrp');
		$this->savegrp->setDecorators($this->_d->savegroup);
	}
	// END

	/* ------------------------------ *
	 *	GET | SET
	 * ------------------------------ */
	public function setAccount($account = array()) {
		$this->_account = $account;
	}
	public function getAccount() {
		return $this->_account;
	}
	public function setUser($user = null) {
		$this->_user = $user;
	}
	public function getUser() {
		return $this->_user;
	}
	// END

	/* ------------------------------ *
	 *	Add Validators
	 * ------------------------------ */
	public function addValidators() {
		$this->getElement('username')->addValidators(array(array('isUnique', true, array('options' => array('table' => 'Users', 'field' => 'username', 'match' => 'id', 'key' => $this->_account['id']), 'messages' => array('notUnique' => 'User name is used.')))));
		$this->getElement('screenname')->addValidators(array(array('isUnique', true, array('options' => array('table' => 'Users', 'field' => 'screenname', 'match' => 'id', 'key' => $this->_account['id']), 'messages' => array('notUnique' => 'Screen name is used.')))));
		$this->getElement('authenticatepassword')->addValidators(array(array('Password', true, array('options' => array('id' => $this->_user->id)))));
	}
	// END

	/* ------------------------------ *
	 *	Set Admin Params
	 * ------------------------------ */
	public function setAdminParams() {
		if( $this->_user->role != Core_Acl_Roles::ADMINS ) {
			$this->removeElement('group_id');
			$this->removeElement('active');
		}
	}
	// END

	/* ------------------------------ *
	 *	Cleans Values
	 * ------------------------------ */
	public function cleanValues($values) {
		foreach($values as $key => $val) {
			if( empty($val) ) {
				unset($values[$key]);
			}
		}
		return $values;
	}
	// END

	/* ------------------------------ *
	 *	Multi Options
	 * ------------------------------ */
	private function _multiOptions($switch = '', $var = null) {
		$options = array('' => 'Select...');

		switch($switch) {
			case 'group_id':
				$goptions 	= Core_Acl_Roles::getGroupOptions();
				$options 	= $goptions;
			break;
		}

		return $options;
	}
	// END
}
// END CLASS



// VALIDATORS
require_once 'Zend/Validate/Abstract.php';

class Zend_Validate_Password extends Zend_Validate_Abstract {
	const INVALID_PASSWORD 			= 'invalidPassword';
	const MALFORMED_PASSWORD 		= 'malformedQuery';
	protected $_options 			= array();
	protected $_messageTemplates 	= array(
		self::INVALID_PASSWORD 		=> "Invalid password",
		self::MALFORMED_PASSWORD 	=> "Malformed Query"
	);
	public function __construct($options = array()) {
		$this->setOptions($options);
	}
	public function getOptions() {
		return $this->_options;
	}
	public function setOptions($options = array()) {
		$this->_options = $options['options'];
		return $this;
	}
	public function isValid($value) {
		$this->_setValue($value);
		if( empty($this->_options['id']) ) {
			$this->_error(self::MALFORMED_PASSWORD);
			return false;
		} else {
			$id = $this->_options['id'];
			try {
				$query = Doctrine_Query::create()
										->select('u.*')
										->from("Users u")
										->where("u.id = ?", $id)
										->andWhere("u.password = ?", $this->_value)
										->execute(array(), Doctrine::HYDRATE_ARRAY);
			} catch(Exception $e) {
				Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
			}
			if( empty($query) ) {
				$this->_error(self::INVALID_PASSWORD);
				return false;
			}
		}
		return true;
	}
}

class Zend_Validate_isUnique extends Zend_Validate_Abstract {
	const NOT_UNIQUE 				= 'notUnique';
	const MALFORMED 				= 'malformedQuery';
	protected $_options 			= array();
	protected $_messageTemplates 	= array(
		self::NOT_UNIQUE 	=> "Not Unique",
		self::MALFORMED 	=> "Malformed Query"
	);
	public function __construct($options = array()) {
		$this->setOptions($options);
	}
	public function getOptions() {
		return $this->_options;
	}
	public function setOptions($options = array()) {
		$this->_options = $options['options'];
		return $this;
	}
	public function isValid($value) {
		$this->_setValue($value);
		if( empty($this->_options['table']) || empty($this->_options['field']) || empty($this->_options['match']) || ! isset($this->_options['key']) ) {
			$this->_error(self::MALFORMED);
			return false;
		} else {
			$table 	= $this->_options['table'];
			$field 	= $this->_options['field'];
			$match 	= $this->_options['match'];
			$key 	= $this->_options['key'];
			try {
				$query = Doctrine_Query::create()
										->select('x.*')
										->from("$table x")
										->where("x.$field = ?", $this->_value)
										->andWhere("x.$match != ?", $key)
										->execute(array(), Doctrine::HYDRATE_ARRAY);
			} catch(Exception $e) {
				Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
			}
			if( empty($query) ) {
				return true;
			} else {				
				$this->_error(self::NOT_UNIQUE);
				return false;
			}
		}
		return true;
	}
}
// END VALIDATORS



// FILTERS
require_once 'Zend/Filter/Interface.php';

class Zend_Filter_Hash implements Zend_Filter_Interface {
	public function __construct() {}
	public function filter($value) {
		return (empty($value)) ? '':md5($value);
	}
}
// END FILTERS


/* End of file Account.php */
/* Location: ./application/forms/Account.php */