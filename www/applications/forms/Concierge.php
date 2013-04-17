<?php

/**
 *	Concierge 
 */
class Form_Concierge extends Zend_Form {

	protected $_config;
	protected $_d;

	/**
	 *	Initiate
	 */
	public function init() {

		$this->_config 	= Zend_Registry::get('config');
		$this->_d 		= new Core_Decorators();

		$this->setAttrib('id', 'concierge_form')->setMethod('post');

		$this->addElement('hash', 'no_csrf_concierge', array('ignore' => true, 'salt' => 'unique', 'decorators' => $this->_d->hidden));

		$this->addElement('submit', 'build', 
												array(
															'ignore' 			=> true,
															'decorators' 		=> $this->_d->hidden,
															'label' 			=> 'Build',
															'class' 			=> 'submit'
													)
						);
	}
	// END

	/* ------------------------------ *
	 *	Default Decorators
	 * ------------------------------ */
	public function loadDefaultDecorators() {
		$this->setDecorators($this->_d->default);

		$this->addDisplayGroup(array('build'), 'buildgrp');
		$this->buildgrp->setDecorators($this->_d->savegroup);
	}
	// END

}
// END CLASS


/* End of file Concierge.php */
/* Location: ./application/forms/Concierge.php */