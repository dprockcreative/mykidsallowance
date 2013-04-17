<?php 

class Core_Decorators {

	public $default 	= array('FormElements', 'Form');

	public $hidden 		= array('ViewHelper');

	public $element 	= array(
								'ViewHelper', 
								array(
										'Errors', 
										array('escape' => false)
									), 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'dd', 'class' => 'element')
									), 
								array(
										'Description', 
										array('tag' => 'dd', 'class' => 'element-description', 'escape' => false)
									),
								array(
										'Label', 
										array('tag' => 'dt', 'class' => 'label', 'escape' => false)
									),
								array(
										array('row' => 'HtmlTag'), 
										array('tag' => 'dl', 'class' => 'form')
									) 
								);

	public $sm_element 	= array(
								'ViewHelper', 
								array(
										'Errors', 
										array('escape' => false)
									), 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'dd', 'class' => 'element')
									), 
								array(
										'Description', 
										array('tag' => 'dd', 'class' => 'element-description', 'escape' => false)
									),
								array(
										'Label', 
										array('tag' => 'dt', 'class' => 'label', 'escape' => false)
									),
								array(
										array('row' => 'HtmlTag'), 
										array('tag' => 'dl', 'class' => 'form small')
									) 
								);

	public $tiny_element 	= array(
								'ViewHelper', 
								array(
										'Errors', 
										array('escape' => false)
									), 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'dd', 'class' => 'element')
									), 
								array(
										'Description', 
										array('tag' => 'dd', 'class' => 'element-description', 'escape' => false)
									),
								array(
										'Label', 
										array('tag' => 'dt', 'class' => 'label', 'escape' => false)
									),
								array(
										array('row' => 'HtmlTag'), 
										array('tag' => 'dl', 'class' => 'form tiny')
									) 
								);

	public $teeny_element 	= array(
								'ViewHelper', 
								array(
										'Errors', 
										array('escape' => false)
									), 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'dd', 'class' => 'element')
									), 
								array(
										'Description', 
										array('tag' => 'dd', 'class' => 'element-description', 'escape' => false)
									),
								array(
										'Label', 
										array('tag' => 'dt', 'class' => 'label', 'escape' => false)
									),
								array(
										array('row' => 'HtmlTag'), 
										array('tag' => 'dl', 'class' => 'form teeny')
									) 
								);

	public $cb_element 	= array(
								'ViewHelper', 
								array(
										'Errors', 
										array('escape' => false)
									), 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'dd', 'class' => 'element')
									), 
								array(
										'Label', 
										array('placement' => 'APPEND', 'tag' => 'dd', 'class' => 'dd-label', 'escape' => false)
									),
								array(
										'Description', 
										array('placement' => 'PREPEND', 'tag' => 'dt', 'class' => 'element-description', 'escape' => false)
									),
								array(
										array('row' => 'HtmlTag'), 
										array('tag' => 'dl', 'class' => 'form small')
									) 
								);

	public $stack_element = array(
								'ViewHelper', 
								array(
										'Errors', 
										array('escape' => false)
									), 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'dd', 'class' => 'element')
									), 
								array(
										'Description', 
										array('tag' => 'dd', 'class' => 'element-description', 'escape' => false)
									),
								array(
										'Label', 
										array('tag' => 'dt', 'class' => 'label', 'escape' => false)
									),
								array(
										array('row' => 'HtmlTag'), 
										array('tag' => 'dl', 'class' => 'form stack')
									) 
								);

	public $save_element 	= array(
								'ViewHelper', 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'dd', 'class' => 'element')
									), 
								array(
										'Description', 
										array('placement' => 'PREPEND', 'tag' => 'dt', 'class' => 'label', 'escape' => false)
									),
								array(
										array('row' => 'HtmlTag'), 
										array('tag' => 'dl', 'class' => 'form')
									) 
								);


	public $group 		= array(
								'FormElements', 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'div', 'class' => 'outline')
									),
								array(
										'Description', 
										array('placement' => 'APPEND', 'tag' => 'dfn', 'class' => 'description', 'escape' => false)
									), 
								array(
										'Fieldset', 
										array('class' => 'oform')
									)
								);

	public $simple_group = array(
								'FormElements', 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'div', 'class' => 'outline')
									),
								array(
										'Description', 
										array('placement' => 'APPEND', 'tag' => 'span', 'class' => 'description', 'escape' => false)
									), 
								array(
										'Fieldset', 
										array('class' => 'oform')
									)
								);

	public $order_group = array(
								'FormElements', 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'div', 'class' => 'outline')
									),
								array(
										'Description', 
										array('placement' => 'PREPEND', 'tag' => 'dfn', 'class' => 'description', 'escape' => false)
									), 
								array(
										'Fieldset', 
										array('class' => 'oform order-group', 'title' => 'Click & Drag to Re-Order', 'escape' => false)
									)
								);

	public $new_group 	= array(
								'FormElements', 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'div', 'class' => 'outline')
									),
								array(
										'Description', 
										array('placement' => 'PREPEND', 'tag' => 'dfn', 'class' => 'description', 'escape' => false)
									), 
								array(
										'Fieldset', 
										array('class' => 'oform new_group')
									)
								);


	public $savegroup 	= array(
								'FormElements', 
								array(
										array('data' => 'HtmlTag'), 
										array('tag' => 'p', 'class' => 'save')
									)
								);

	public function __construct() {}
}
// END CLASS