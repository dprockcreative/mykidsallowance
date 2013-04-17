<?php 
class Form_AdminIssueComment extends Zend_Form {

	protected $_d;
	protected $_id;

	/**
	 *	Initiate
	 */
	public function init() {

		$this->_d 		= new Core_Decorators();

		$this->setMethod('post');

		$this->addElement('checkbox', 'active', 
												array(
															'required' 			=> false,
															'decorators' 		=> $this->_d->simple_cb,
															'label' 			=> 'Active',
															'class' 			=> 'checkbox',
															'unCheckedValue' 	=> NULL
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
		$this->setAttribs(array('id' => 'update_issue_comment_'.$this->_id, 'class' => 'update-issue-comment'))->setAction('/issues/updateissuecomment/id/'.$this->_id.'/');
	}
	// END

}
// END CLASS