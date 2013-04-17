<?php

/**
 *	Allowance Configurations
 */
class Form_AllowanceConfigs extends Zend_Form_SubForm {

	protected $_config;
	protected $_d;
	protected $_user;

	private $_percentage_limit = 50;

	/**
	 *	Init
	 */
	public function init() {

		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->addElement('hidden', 'allowance_id', array('decorators' => $this->_d->hidden, 'class' => 'allowance_id'));

		$this->addElement('select', 'config_id', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Type',
															'class' 			=> 'select select-lrg',
															'multiOptions' 		=> $this->_multiOptions('config_id')
													)
						);

		$this->addElement('text', 'amount', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->tiny_element,
															'label' 			=> 'Amount (10.20)',
															'class' 			=> 'text text-tiny',
															'maxlength' 		=> 7,
															'filters' 			=> array(array('Float')),
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(1, 7, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
						);

		$this->addElement('select', 'percent', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->tiny_element,
															'label' 			=> 'Percent',
															'description' 		=> 'deductions only; overrides amount',
															'class' 			=> 'select select-tiny',
															'multiOptions' 		=> $this->_multiOptions('percent')
													)
						);

		$this->addElement('select', 'percent', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->tiny_element,
															'label' 			=> 'Percent',
															'description' 		=> 'deductions only; overrides amount',
															'class' 			=> 'select select-tiny',
															'multiOptions' 		=> $this->_multiOptions('percent')
													)
						);

		$this->addElement('checkbox', 'active', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->cb_element,
															'label' 			=> 'Active ?',
															'description' 		=> '&nbsp;',
															'unCheckedValue' 	=> null,
															'class' 			=> 'checkbox'
													)
						);

	}
	// END

	/* ------------------------------ *
	 *	DEFAULT DECORATORS
	 * ------------------------------ */
	public function loadDefaultDecorators() {
		$this->setDecorators($this->_d->order_group);
	}
	// END

	/* ------------------------------ *
	 *	Multi Options
	 * ------------------------------ */
	private function _multiOptions($switch = '', $var = null) {
		$options = array('' => 'Select...');

		switch($switch) {
			case 'config_id':
				$auth 		= Zend_Auth::getInstance();
				$user 		= $auth->getIdentity();
				$options 	+= Configurations::getOptionsByUserId($user->id);
			break;
			case 'percent':
				$options = array('' => 'n/a');
				for($i = 1;$i <= $this->_percentage_limit; $i++) {
					$p = $i*0.01;
					$options["$p"] = "$i %";
				}
			break;
		}

		return $options;
	}
	// END

}
// END CLASS

/* End of file AllowanceConfigs.php */
/* Location: ./application/forms/AllowanceConfigs.php */