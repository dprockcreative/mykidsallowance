<?php

class AdminController extends Zend_Controller_Action {

	public $data;
	public $config;
	public $ip;

	public $user;

	protected $_account;

	/**
	 *	Init
	 */
	public function init() {
		$this->_helper->layout->setLayout('layout');	
		$this->data 			= new stdClass;
		$this->data->config 	= Zend_Registry::get('config');
		$this->ip 				= $this->getRequest()->getServer('REMOTE_ADDR');

		$auth 					= Zend_Auth::getInstance();
		$this->user 			= $auth->getIdentity();
		$this->data->user 		= $this->user;

		//Core_P::p($this->_getAllParams(), 1);

		$this->_helper->getHelper('FlashMessenger');

		$this->view->headTitle()->prepend('Admin');
		$this->view->headMeta()
			->appendHttpEquiv('pragma', 'no-cache')
			->appendHttpEquiv('cache-control', 'max-age=0')
			->appendHttpEquiv('cache-control', 'no-cache')
			->appendHttpEquiv('cache-control', 'no-store')
			->appendName('robots', 'none');

	}
	// END

	/**
	 *	ACTION = Index
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function indexAction() {

		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = Users
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function usersAction() {
		$this->data->users 	= Users::getUsers();
		$this->view->data 	= $this->data;
	}
	// END

	/**
	 *	ACTION = Profile
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function profileAction() {

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$id = $this->_getParam('id');

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$user = Users::getAdminUserById($id);

		$this->data->is_detail = true;

		/* ------------------------------ *
		 * 	FORM
		 * ------------------------------ */
		$this->_profile_form($user);

		$this->data->admin = Core_Helper::compile_data($this->data, $user, 'user');

		$this->view->headTitle()->prepend('Profile');
		$this->view->headTitle()->prepend($user->screenname);

		/* ------------------------------ *
		 * 	OUTPUT
		 * ------------------------------ */
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = Allowances
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function allowancesAction() {

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$user_id 	= $this->_getParam('user_id');
		$active 	= $this->_getParam('active');
		$active 	= ( empty($active) || ! is_int($active) ) ? array(0,1):$active;

		$this->data->is_detail = ( empty($user_id) ) ? false:true;

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$allowances = Allowances::getAllowances($user_id, $active);

		$this->data = Core_Helper::compile_data($this->data, $allowances, 'allowances');

		$this->view->headTitle()->prepend('Allowances');

		if( ! empty($this->data->is_detail) ) {
			$user = Users::getAdminUserById($user_id);
			$this->data->admin = Core_Helper::compile_data($this->data, $user, 'user');
			$this->view->headTitle()->prepend($user->screenname);
		}

		/* ------------------------------ *
		 * 	OUTPUT
		 * ------------------------------ */
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = Allowance
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function allowanceAction() {

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$id 	= $this->_getParam('id');

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$allowance = Allowances::getAdminAllowanceById($id);

		//Core_P::p($allowance->{0}->toArray(), 1);

		/* ------------------------------ *
		 * 	VALIDATE
		 * ------------------------------ */
		if( empty($allowance) ) {
			throw new Zend_Exception("No record");
		}

		/* ------------------------------ *
		 *	PROCESS
		 * ===============================
		 *	Allowance
		 * ------------------------------ */
		$this->_allowance_form($allowance);

		$this->data = Core_Helper::compile_data($this->data, $allowance->{0}->toArray(), 'allowance');

		$user = $allowance->{0}->Users;
		$this->data->admin = Core_Helper::compile_data($this->data, $user, 'user');
		$this->view->headTitle()->prepend($user->screenname);

		/* ------------------------------ *
		 * 	TITLES
		 * ------------------------------ */
		$this->view->headTitle()->prepend($allowance->{0}->label);
		$this->view->headTitle()->prepend('Set Up');

		/* ------------------------------ *
		 * 	OUTPUT
		 * ------------------------------ */
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = Settings
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function settingsAction() {

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$user_id = $this->_getParam('user_id');
		$active = $this->_getParam('active');
		$active = ( empty($active) || ! is_int($active) ) ? array(0,1):$active;

		$this->data->is_detail = ( empty($user_id) ) ? false:true;

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$settings = Configurations::getConfigurations($user_id);

		$this->data = Core_Helper::compile_data($this->data, $settings, 'settings');

		$this->view->headTitle()->prepend('Settings');

		if( ! empty($this->data->is_detail) ) {
			$user = Users::getAdminUserById($user_id);
			$this->data->admin = Core_Helper::compile_data($this->data, $user, 'user');
			$this->view->headTitle()->prepend($user->screenname);
		}

		/* ------------------------------ *
		 * 	OUTPUT
		 * ------------------------------ */
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = Setting
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function settingAction() {
		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$id 	= $this->_getParam('id');

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$configuration = Configurations::getConfigurationById($id);

		//Core_P::p($configuration->{0}->toArray(), 1);

		/* ------------------------------ *
		 * 	VALIDATE
		 * ------------------------------ */
		if( empty($configuration) ) {
			throw new Zend_Exception("No record");
		}

		/* ------------------------------ *
		 *	PROCESS
		 * ===============================
		 *	Configuration
		 * ------------------------------ */
		$this->_configuration_form($configuration);


		$this->data = Core_Helper::compile_data($this->data, $configuration->{0}->toArray(), 'configuration');

		$user = $configuration->{0}->Users;
		$this->data->admin = Core_Helper::compile_data($this->data, $user, 'user');
		$this->view->headTitle()->prepend($user->screenname);

		/* ------------------------------ *
		 * 	TITLES
		 * ------------------------------ */
		$this->view->headTitle()->prepend($configuration->{0}->label);
		$this->view->headTitle()->prepend('Setting');

		/* ------------------------------ *
		 * 	OUTPUT
		 * ------------------------------ */
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	FM - Dispatch
	 *
	 *	@Sets Flash Messages
	 */
	public function postDispatch() {
		$this->data->fms = $this->_helper->_FlashMessenger->getMessages();
	}
	// END
/*
 * ===========================
 *	Private functions
 * ===========================
 */

	/**
	 *	User Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _profile_form($user) {

		$form = new Form_AdminUser();
		$form->setUser($user);
		$form->setMember($user->Members);

		/* ------------------------------ *
		 * 	ROUTE POST
		 * ------------------------------ */
		if( $this->getRequest()->isPost() ) {

			$post 	= $this->_request->getPost();
			$values = $form->populate($post)->getValues();
			$values = $form->cleanValues($values);

			//Core_P::p($values, 1);

			/**
			 * 	Valid Post
			 */
			if( $form->isValid($post) ) {

				$values['updated'] = date("Y-m-d H:i:s", time());

				$user->fromArray($values);
				$user->save();

				if( ! $user->trySave() ) {
					$this->_helper->_FlashMessenger->addMessage(array('error' => "User not saved."));
				}
				else {
					$this->_helper->_FlashMessenger->addMessage(array('notice' => "User saved."));
				}


			}
			else {
				$this->_helper->_FlashMessenger->addMessage(array('error' => "Errors."));
			}
		} else {
			$pop = $user->toArray();
			$form->populate($pop);
		}

		$this->data->profile->form = $form;
	}
	// END

	/**
	 *	Manage Allowance Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _allowance_form($allowance) {

		$form = new Form_Allowance();

		$form->setAllowance($allowance->{0});
		$form->buildAllowanceConfigs();
		$form->addSaveGrp();

		/* ------------------------------ *
		 * 	ROUTE POST
		 * ------------------------------ */
		if( $this->getRequest()->isPost() ) {

			$post 	= $this->_request->getPost();
			$values = $form->populate($post)->getFormValues();
			$new 	= $values['new'];unset($values['new']);

			if( ! empty($new['config_id']) && empty($new['percent']) ) {
				$form->new->getElement('amount')->setRequired(true);
			}

			//Core_P::p($values, 1);
			/**
			 * 	Valid Post
			 */
			if( $form->isValid($post) ) {

				//Core_P::p($new, 1);
				$data = array(
								'updated' 	=> date('Y-m-d H:i:s'),
								'editor_id' => $this->user->id
							);

				$data = $values+$data;
				$data['user_id'] = $allowance->{0}->user_id;

				$allowance->{0}->fromArray($data);

				$allowance->save();

				if( ! $allowance->trySave() ) {
					$this->_helper->_FlashMessenger->addMessage(array('error' => "Allowance not updated."));
				}
				else {
					$this->_helper->_FlashMessenger->addMessage(array('notice' => "Allowance updated."));

					/**
					 * 	New AllowanceConfigs
					 */
					if( ! empty($new['config_id']) && ( ! empty($new['amount']) || ! empty($new['percent']) ) ) {
						$new['created'] = date('Y-m-d H:i:s');
						$query = new AllowanceConfigs();
						$query->fromArray($new);
						$query->save();

						if( ! $query->trySave() ) {
							$this->_helper->_FlashMessenger->addMessage(array('error' => "New Allowance Feature not saved."));
						}
						else {
							$this->_helper->_FlashMessenger->addMessage(array('notice' => "New Allowance Feature added.", 'redirect' => '/admin/allowance/id/'.$allowance->id.'/'));
						}
					}
				}
			}
			else {
				$this->_helper->_FlashMessenger->addMessage(array('error' => "Errors."));
			}
		} else {
			$form->populate($allowance->{0}->toArray());
		}

		$this->data->allowance->form = $form;
	}
	// END

	/**
	 *	Configuration Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _configuration_form($configuration) {

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$id = $this->_getParam('id');

		$form = new Form_Configuration();

		$form->setParams($configuration);

		/* ------------------------------ *
		 * 	ROUTE POST
		 * ------------------------------ */
		if( $this->getRequest()->isPost() ) {

			$post 	= $this->_request->getPost();
			$values = $form->populate($post)->getValues();
			$values['action'] = $form->getActionByTypeId($values['type_id']);

			//Core_P::p($values, 1);
			/**
			 * 	Valid Post
			 */
			if( $form->isValid($post) ) {

				$data = $values;
				$data['user_id'] = $configuration->{0}->user_id;
				//Core_P::p($data, 1);

				$configuration->{0}->fromArray($data);
				$configuration->save();

				if( ! $configuration->trySave() ) {
					$this->_helper->_FlashMessenger->addMessage(array('error' => "Settings not updated."));
				}
				else {
					$this->_helper->_FlashMessenger->addMessage(array('notice' => "Settings updated."));

					/**
					 * 	New Settings
					 */
					if( empty($id) ) { 
						$id = $configuration->{0}->id;
						$this->_helper->_FlashMessenger->addMessage(array('notice' => "New Setting saved.", 'redirect' => '/admin/setting/id/'.$id.'/'));
					}
				}
			}
			else {
				$this->_helper->_FlashMessenger->addMessage(array('error' => "Errors."));
			}
		} else {
			$form->populate($configuration->{0}->toArray());
		}

		$this->data->configuration->form = $form;
	}
	// END


}
// END CLASS