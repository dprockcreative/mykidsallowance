<?php

/**
 *	Allowance
 */
class Form_Allowance extends Zend_Form {

	protected $_config;
	protected $_d;
	protected $_allowance;

	/**
	 *	Init
	 */
	public function init() {

		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->setAttrib('id', 'allowance_form')->setMethod('post');

		$this->addElement('hash', 'no_csrf_allowance', array('ignore' => true, 'salt' => 'unique', 'decorators' => $this->_d->hidden));

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

		$this->addElement('select', 'period', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->tiny_element,
															'label' 			=> 'Pay Period',
															'class' 			=> 'select select-tiny',
															'multiOptions' 		=> $this->_multiOptions('period')
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

		$this->addDisplayGroup(array('label', 'period', 'active'), 'allowancegrp', array('legend' => 'Allowance'));
		$this->allowancegrp->setDecorators($this->_d->group);

	}
	// END

	/* ------------------------------ *
	 *	GET | SET
	 * ------------------------------ */
	public function getAllowance() {
		return $this->_allowance;
	}
	public function setAllowance($allowance = null) {
		$this->_allowance = $allowance;

		if( false === AllowanceConfigs::hasBase($this->_allowance->id) ) {
			$this->getElement('active')->setAttrib('disabled','disabled')->setDescription('Disabled until a "Base" type Feature is added');
		}

	}
	// END

	/* ------------------------------ *
	 *	BUILD ALLOWANCE CONFIGS
	 * ------------------------------ */
	public function buildAllowanceConfigs() {

		/**
		 *	Editing
		 */
		$AllowanceConfigs = new Zend_Form_SubForm();
		if( ! empty($this->_allowance->AllowanceConfigs) ) {
			foreach($this->_allowance->AllowanceConfigs as $key => $row) {	
				$subform = new Form_AllowanceConfigs();
				$subform->setIsArray(true)->setLegend("Feature #$row->id")->setAttrib('rel', $row->id);
				$AllowanceConfigs->addSubform($subform, $key);
			}
		}

		$AllowanceConfigs->setDecorators( array('FormElements', array('HtmlTag', array('tag' => 'div', 'id' => 'order_group'))) );
		$this->addSubform($AllowanceConfigs, 'AllowanceConfigs');

		/**
		 *	Adding
		 */
		$subform = new Form_AllowanceConfigs();
		$subform->setIsArray(true)->setLegend("New Feature");
		$subform->getElement('allowance_id')->setValue($this->_allowance->id);
		$subform->setDecorators($this->_d->new_group);

		$this->addSubform($subform, 'new');
	}
	// END

	/* ------------------------------ *
	 *	GET | SET
	 * ------------------------------ */
	public function addSaveGrp() {
		$this->addDisplayGroup(array('reset', 'save'), 'savegrp');
		$this->savegrp->setDecorators($this->_d->savegroup);
	}
	// END

	/* ------------------------------ *
	 *	Get Values
	 * ------------------------------ */
	public function getFormValues() {

		$values = $this->getValues();

		if( false === AllowanceConfigs::hasBase($this->_allowance->id) ) {
			$values['active'] = 0;
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
			case 'period':
				$options = Core_Interval::getIntervalOptions();
			break;
		}

		return $options;
	}
	// END

}
// END CLASS


// FILTERS
require_once 'Zend/Filter/Interface.php';

class Zend_Filter_Float implements Zend_Filter_Interface {
	public function __construct() {}
	public function filter($value) {
		$value = preg_replace('#[^0-9\.]#ui', '', $value);
		return (string) (empty($value)) ? '':$value;
	}
}
// END FILTERS

/* End of file Allowance.php */
/* Location: ./application/forms/Allowance.php */