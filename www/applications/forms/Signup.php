<?php

/**
 *	Signup
 */
class Form_Signup extends Zend_Form {

	protected $_config;
	protected $_d;

	/**
	 *	Init
	 */
	public function init() {

		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->setAttrib('id', 'signup_form')->setMethod('post');

		$this->addElement('hash', 'no_csrf_signup', array('ignore' => true, 'salt' => 'unique', 'decorators' => $this->_d->hidden));

		$this->addElement('hidden', '_signup', array('ignore' => true, 'value' => 1, 'decorators' => $this->_d->hidden));

		$this->addElement('text', 'username', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'User Name',
															'class' 			=> 'text text-lrg',
															'maxlength' 		=> 16,
															'autocomplete' 		=> 'off',
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(6, 16, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							),
																						array(
																								'isUnique', true, array('options' => array('table' => 'Users', 'field' => 'username', 'match' => 'id', 'key' => "0"), 'messages' => array('notUnique' => 'User name is used.'))
																							)
																						)
													)
						);

		$this->addElement('text', 'screenname', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Screen Name',
															'class' 			=> 'text text-lrg',
															'maxlength' 		=> 32,
															'autocomplete' 		=> 'off',
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(3, 32, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							),
																						array(
																								'isUnique', true, array('options' => array('table' => 'Users', 'field' => 'screenname', 'match' => 'id', 'key' => "0"), 'messages' => array('notUnique' => 'Screen name is used.'))
																							)
																						)
													)
						);

		$this->addElement('password', 'password', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Select Password',
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
															'label' 			=> 'Confirm Password',
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

		$this->Members->removeElement('phone1');
		$this->Members->removeElement('phone2');
		$this->Members->removeElement('city');
		$this->Members->removeElement('state');
		$this->Members->removeElement('zipcode');
		$this->Members->removeDisplayGroup('locationgrp');


		$this->addDisplayGroup(array('password', 'confirmpassword'), 'admingrp', array('legend' => 'Credentials'));
		$this->admingrp->setDecorators($this->_d->group);

		$this->addDisplayGroup(array('reset', 'save'), 'savegrp');
		$this->savegrp->setDecorators($this->_d->savegroup);
	}
	// END

	/* ------------------------------ *
	 *	GET | SET
	 * ------------------------------ */
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

}
// END CLASS



// VALIDATORS
require_once 'Zend/Validate/Abstract.php';

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
require_once 'Zend/Loader.php';
class Zend_Filter_Hash implements Zend_Filter_Interface {
	public function __construct() {}
	public function filter($value) {
		return (empty($value)) ? '':md5($value);
	}
}
// END FILTERS


/* End of file Signup.php */
/* Location: ./application/forms/Signup.php */