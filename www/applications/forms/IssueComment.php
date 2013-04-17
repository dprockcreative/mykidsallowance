<?php

/**
 *	Issue Tracker
 */
class Form_IssueComment extends Zend_Form {

	protected $_config;
	protected $_d;

	protected $_user;
	protected $_issue_id;

	/**
	 *	Initiate
	 */
	public function init() {

		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->setMethod('post');

		//$this->addElement('hash', 'no_csrf_issuecomment', array('ignore' => true, 'salt' => 'unique', 'decorators' => $this->_d->hidden));

		$this->addElement('text', 'name', 
												array(
															'required' 			=> true,
															'decorators' 		=> $this->_d->element,
															'label' 			=> 'Name',
															'class' 			=> 'text text-med',
															'maxlength' 		=> 45,
															'validators' 		=> array(array('StringLength', true, array(2, 45, 'messages' => array('stringLengthTooShort' => 'Must be %min%-%max% characters.', 'stringLengthTooLong' => 'Must be %min%-%max% characters.')))))
						);
	
		$this->addElement('textarea', 'comment', 
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
		$this->addDisplayGroup(array('name', 'comment', 'save'), 'commentgrp', array('legend' => 'Add Comment'));
		$this->commentgrp->setDecorators($this->_d->group);
	}
	// END

	/* ------------------------------ *
	 *	GET | SET
	 * ------------------------------ */
	public function setUser($user = null) {
		$this->_user = $user;
		if( ! empty($this->_user->screenname) ) {
			$this->getElement('name')->setValue($this->_user->screenname)->setAttribs(array('readonly' => 'readonly', 'class' => 'text text-med disabled'));
		}
	}
	public function getUser() {
		return $this->_user;
	}
	public function setIssueId($issue_id = null) {
		$this->_issue_id = $issue_id;

		$this->setAttribs(array('id' => 'issuecomment_form_'.$this->_issue_id, 'rel' => $this->_issue_id, 'class' => 'issuecomment-form'))->setAction('/issues/comment/issue_id/'.$this->_issue_id.'/');

		$this->getElement('name')->setAttrib('id', 'comment_name_'.$this->_issue_id);
		$this->getElement('comment')->setAttrib('id', 'comment_'.$this->_issue_id);
	}
	public function getIssueId() {
		return $this->_issue_id;
	}
	public function setMessage($message = array()) {

		$key = key($message);
		$msg = $message[$key];
		$this->commentgrp->setDescription($msg);
	}
	// END

}
// END CLASS



/* End of file IssueComment.php */
/* Location: ./application/forms/IssueComment.php */