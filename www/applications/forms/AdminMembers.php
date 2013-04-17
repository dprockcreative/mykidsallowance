<?php

/**
 *	AdminMembers
 */
class Form_AdminMembers extends Zend_Form_SubForm {

	protected $_config;
	protected $_d;

	/**
	 *	Init
	 */
	public function init() {

		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->addElement('text', 'email', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Email Address',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 64,
															'validators' 		=> array(
																						array(
																								'EmailAddress', true, array('messages' => $this->_email_messages)
																							)
																						)
													)
												);


		$this->addElement('text', 'phone1', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Primary Phone',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 20,
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(7, 20, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
												);

		$this->addElement('text', 'phone2', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Secondary Phone',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 20,
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(7, 20, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
												);


		$this->addElement('text', 'city', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'City',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 64,
															'validators' 		=> array(
																						)
													)
												);

		$this->addElement('select', 'state', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'State',
															'class' 			=> 'select select-med',
															'multiOptions' 		=> $this->_multiOptions('state')
													)
						);

		$this->addElement('text', 'zipcode', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Zip Code',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 10,
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(5, 10, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
												);

	}
	// END

	/* ------------------------------ *
	 *	DEFAULT DECORATORS
	 * ------------------------------ */
	public function loadDefaultDecorators() {
		$this->setDecorators(array('FormElements'));

		$this->addDisplayGroup(array('email', 'phone1', 'phone2'), 'contactgrp', array('legend' => 'Contact'));
		$this->contactgrp->setDecorators($this->_d->group);

		$this->addDisplayGroup(array('city', 'state', 'zipcode'), 'locationgrp', array('legend' => 'Location'));
		$this->locationgrp->setDecorators($this->_d->group);

	}
	// END

	/* ------------------------------ *
	 *	Multi Options
	 * ------------------------------ */
	private function _multiOptions($switch = '', $var = null) {
		$options = array('' => 'Select...');

		switch($switch) {
			case 'state':
				$soptions 	= Geoloc::getStateOptions();
				$options 	= $options+$soptions;
			break;
		}

		return $options;
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


/* End of file Members.php */
/* Location: ./application/forms/Members.php */