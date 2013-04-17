<?php 
class Form_AdminIssue extends Zend_Form {

	protected $_d;
	protected $_id;

	/**
	 *	Initiate
	 */
	public function init() {

		$this->_d = new Core_Decorators();

		$this->setMethod('post');

		$this->addElement('select', 'status', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->simple,
															'label' 			=> 'Status',
															'class' 			=> 'select mini-select select-tiny',
															'multiOptions' 		=> $this->_multiOptions('status')
													)
						);

		$this->addElement('submit', 'save', 
												array(
															'ignore' 			=> true,
															'decorators' 		=> $this->_d->simple_save,
															'label' 			=> 'Save',
															'class' 			=> 'tiny-submit',
															'description' 		=> '&nbsp;'
													)
						);
	}
	// END

	/* ------------------------------ *
	 *	Default Decorators
	 * ------------------------------ */
	public function loadDefaultDecorators() {
		$this->setDecorators($this->_d->default);
	}
	// END

	/* ------------------------------ *
	 *	GET | SET
	 * ------------------------------ */
	public function setId($id) {
		$this->_id = $id;
		$this->setAttribs(array('id' => 'update_issue_'.$this->_id, 'class' => 'update-issue'))->setAction('/issues/updateissue/id/'.$this->_id.'/');
	}
	// END

	/* ------------------------------ *
	 *	Multi Options
	 * ------------------------------ */
	private function _multiOptions($switch = '', $var = null) {
		$options = array('' => 'Select...');

		switch($switch) {
			case 'status':
				$options = Core_Issues::getStatusOptions();
			break;
		}

		return $options;
	}
	// END

}
// END CLASS