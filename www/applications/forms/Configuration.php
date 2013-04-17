<?php

/**
 *	Configuration
 */
class Form_Configuration extends Zend_Form {

	protected $_config;
	protected $_d;
	protected $_params;

	/**
	 *	Init
	 */
	public function init() {
		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->addElement('hash', 'no_csrf_configuration', array('ignore' => true, 'salt' => 'unique', 'decorators' => $this->_d->hidden));

		$this->addElement('hidden', 'action', array('decorators' => $this->_d->hidden));

		$this->addElement('select', 'type_id', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Type',
															'class' 			=> 'select select-med',
															'multiOptions' 		=> $this->_multiOptions('type_id')
													)
						);

		$this->addElement('text', 'label', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Label',
															'class' 			=> 'text text-lrg',
															'maxlength' 		=> 45,
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(2, 45, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
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

		$this->addDisplayGroup(array('type_id', 'label'), 'settingsgrp');
		$this->settingsgrp->setDecorators($this->_d->group);

		$this->addDisplayGroup(array('reset', 'save'), 'savegrp');
		$this->savegrp->setDecorators($this->_d->savegroup);
	}
	// END

	/* ------------------------------ *
	 *	Set Params
	 * ------------------------------ */
	public function setParams($params) {
		$this->_params = $params;

		if( empty($this->_params->{0}->id) ) {
			$this->settingsgrp->setLegend('Add');
		} else {
			$this->settingsgrp->setLegend('Edit');
		}
	}
	// END

	/* ------------------------------ *
	 *	Get Action By Type ID
	 * ------------------------------ */
	public function getActionByTypeId($type_id) {
		$cp = new Core_Pay();
		return $cp->types[$type_id];
	}
	// END

	/* ------------------------------ *
	 *	Multi Options
	 * ------------------------------ */
	private function _multiOptions($switch = '', $var = null) {
		$options = array('' => 'Select...');

		switch($switch) {
			case 'action':
				$options = Core_Pay::getActionOptions();
			break;
			case 'type_id':
				$options = Types::getTypeOptions();
			break;
			case 'user_id':
				$options = Users::getUserOptions();
			break;
		}

		return $options;
	}
	// END

}
// END CLASS

/* End of file Configuration.php */
/* Location: ./application/forms/Configuration.php */