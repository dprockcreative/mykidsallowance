<?php

/**
 *	Issue Tracker
 */
class Form_Issue extends Zend_Form {

	protected $_config;
	protected $_d;

	protected $_user;

	/**
	 *	Initiate
	 */
	public function init() {

		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->setAttrib('id', 'issuetracker_form')->setAction('/issues')->setMethod('post');

		$this->addElement('hash', 'no_csrf_issuetracker', array('ignore' => true, 'salt' => 'unique', 'decorators' => $this->_d->hidden));

		$this->addElement('text', 'name', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Name',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 45,
															'validators' 		=> array(
																						array(
																								'StringLength', true, array(2, 45, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.'))
																							)
																						)
													)
						);

		$this->addElement('select', 'topic', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Topic',
															'class' 			=> 'select select-small',
															'multiOptions' 		=> $this->_multiOptions('topics')
													)
						);

		$this->addElement('textarea', 'issue', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Issue',
															'class' 			=> 'textarea auto-textarea'
													)
						);

		$this->addElement('submit', 'save', 
												array(
															'ignore' 			=> true,
															'decorators' 		=> $this->_d->save_element,
															'label' 			=> 'Save',
															'class' 			=> 'mini-submit',
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

		$this->addDisplayGroup(array('name', 'topic', 'issue', 'save'), 'issuegrp', array('legend' => 'Add Issue'));
		$this->issuegrp->setDecorators($this->_d->group);
	}
	// END

	/* ------------------------------ *
	 *	GET | SET
	 * ------------------------------ */
	public function setUser($user = null) {
		$this->_user = $user;

		//Core_P::p($this->_user, 0);

		if( ! empty($this->_user->screenname) ) {
			$this->getElement('name')->setValue($this->_user->screenname)->setAttribs(array('readonly' => 'readonly', 'class' => 'text text-med disabled'));
		}

	}
	public function getUser() {
		return $this->_user;
	}
	public function setMessage($message = array(), $redirect = '') {
		$cls = key($message);
		$msg = $message[$cls].'<img src="/assets/img/bump.gif" alt="" onload=\'_timer(2, "redirect", "'.$redirect.'");\' />';
		$this->issuegrp->setDescription($msg);
	}
	// END

	/* ------------------------------ *
	 *	Multi Options
	 * ------------------------------ */
	private function _multiOptions($switch = '', $var = null) {
		$options = array('' => 'Select...');

		switch($switch) {
			case 'topics':
				$options = Core_Issues::getTopicOptions();
			break;
			case 'status':
				$options = Core_Issues::getStatusOptions();
			break;
		}

		return $options;
	}
	// END

}
// END CLASS



/* End of file Issue.php */
/* Location: ./application/forms/Issue.php */