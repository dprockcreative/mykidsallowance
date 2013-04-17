<?php 

class AllowancesController extends Zend_Controller_Action {

	public $data;
	public $config;
	public $ip;
	public $user;

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
		$this->data->activity 	= Users::getActivity($this->user->id);

		$this->_helper->getHelper('FlashMessenger');

		$this->view->headTitle()->prepend('Allowances');
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
	 */
	public function indexAction() {

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$active = $this->_getParam('active');
		$active = ( empty($active) || ! is_int($active) ) ? array(0,1):$active;

		/* ------------------------------ *
		 * 	VALIDATE & QUERY
		 * ------------------------------ */
		$allowances = Allowances::getAllowancesByUserId($this->user->id, $active);

		if( $this->user->role == Core_Acl_Roles::ADMINS ) {
			$this->_helper->_FlashMessenger->addMessage(array('notice' => 'Admins click <a href="/admin/allowances">admin</a>'));
		}

		/* ------------------------------ *
		 * 	PROCESS
		 * ------------------------------ */
		$this->data = Core_Helper::compile_data($this->data, $allowances, 'allowances');

		$this->data->userHasSettings 	= $this->_userHasSettings();
		$this->data->userHasAllowances 	= $this->_userHasAllowances();

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
		$owner 	= ( empty($id) ) ? true:false;

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$allowance = Allowances::getAllowanceById($id);

		/* ------------------------------ *
		 * 	VALIDATE
		 * ------------------------------ */
		if( empty($allowance) ) {
			throw new Zend_Exception("No record");
		} elseif( ! empty($allowance->{0}->id) ) {
			$owner = $this->_isOwner($allowance->{0}->user_id);
			if( false === $owner && $this->user->role != Core_Acl_Roles::ADMINS ) {
				throw new Zend_Exception("You are not authorized to view this record");
			}
		}

		/* ------------------------------ *
		 *	PROCESS
		 * ===============================
		 *	Allowance
		 * ------------------------------ */
		$this->_allowance_form($allowance, $owner);

		$this->data = Core_Helper::compile_data($this->data, $allowance->{0}->toArray(), 'allowance');

		$this->data->userHasAllowances 	= $this->_userHasAllowances();

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
	 *	ACTION = Stub
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function stubAction() {

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$id 	= $this->_getParam('id');
		$owner 	= ( empty($id) ) ? true:false;

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$allowance = Allowances::getAllowanceByIdForStub($id);

		/* ------------------------------ *
		 * 	VALIDATE
		 * ------------------------------ */
		if( empty($allowance) ) {
			throw new Zend_Exception("No record");
		} elseif( ! empty($allowance->{0}->id) ) {
			$owner = $this->_isOwner($allowance->{0}->user_id);
			if( false === $owner && $this->user->role != Core_Acl_Roles::ADMINS ) {
				throw new Zend_Exception("You are not authorized to view this record");
			}
		}

		/* ------------------------------ *
		 *	PROCESS
		 * ===============================
		 *	Allowance
			Core_P::p($allowance->{0}->toArray());
		 * ------------------------------ */
		$allowance = $allowance->{0};

		/* ------------------------------ *
		 *	PROCESS
		 * ===============================
		 *	Interval
			Core_P::p($allowance, 1);
		 * ------------------------------ */
		$ci 		= new Core_Interval(1);
		$interval 	= $ci->generateInterval($allowance);

		/* ------------------------------ *
		 *	PROCESS
		 * ===============================
		 *	Pay
		 * ------------------------------ */
		$cp = new Core_Pay(array('allowance' => $allowance, 'interval' => $interval));

		$this->data = Core_Helper::compile_data($this->data, $allowance, 'allowance');
		$this->data = Core_Helper::compile_data($this->data, $cp->getPay(), 'pay');

		/* ------------------------------ *
		 * 	TITLES
		 * ------------------------------ */
		$this->view->headTitle()->prepend($allowance->label);
		$this->view->headTitle()->prepend('Current Stub');

		/* ------------------------------ *
		 * 	OUTPUT
		 * ------------------------------ */
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = History
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function historyAction() {

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$id 	= $this->_getParam('id');
		$owner 	= ( empty($id) ) ? true:false;

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$allowance = Allowances::getAllowanceByIdForStub($id);

		/* ------------------------------ *
		 * 	VALIDATE
		 * ------------------------------ */
		if( empty($allowance) ) {
			throw new Zend_Exception("No record");
		} elseif( ! empty($allowance->{0}->id) ) {
			$owner = $this->_isOwner($allowance->{0}->user_id);
			if( false === $owner && $this->user->role != Core_Acl_Roles::ADMINS ) {
				throw new Zend_Exception("You are not authorized to view this record");
			}
		}

		/* ------------------------------ *
		 *	PROCESS
		 * ===============================
		 *	Allowance
		 * ------------------------------ */
		$allowance = $allowance->{0};

		/* ------------------------------ *
		 *	PROCESS
		 * ===============================
		 *	Pay
		 * ------------------------------ */
		$cph = new Core_Pay_History($allowance);

		$this->data = Core_Helper::compile_data($this->data, $allowance, 'allowance');
		$this->data = Core_Helper::compile_data($this->data, $cph->getPayHistory(), 'history');

		/* ------------------------------ *
		 * 	TITLES
		 * ------------------------------ */
		$this->view->headTitle()->prepend($allowance->label);
		$this->view->headTitle()->prepend('History');

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
		$this->_forward('allowancesettings', 'configurations', null, array('data' => $this->data));
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
		$this->_forward('allowancesetting', 'configurations', null, array('data' => $this->data));
	}
	// END

	/**
	 *	ACTION = Concierge
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function conciergeAction() {
		$this->_forward('concierge', 'configurations', null, array('data' => $this->data));
	}
	// END
/*
 * ===========================
 *	Private functions
 * ===========================
 */

	/**
	 *	Manage Allowance Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _allowance_form($allowance, $owner = false) {

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
				$data['user_id'] = ( $owner ) ? $this->user->id:$allowance->{0}->user_id;

				$allowance->{0}->fromArray($data);

				if( ! $allowance->trySave() ) {
					$this->_helper->_FlashMessenger->addMessage(array('error' => "Allowance not updated."));
				}
				else {
					$this->_helper->_FlashMessenger->addMessage(array('notice' => "Allowance updated."));

					/**
					 * 	New AllowanceConfigs
					 */
					if( ! empty($allowance->{0}->id) && ! empty($new['config_id']) && ( ! empty($new['amount']) || ! empty($new['percent']) ) ) {
						$new['allowance_id'] = $allowance->{0}->id;
						$new['created'] = date('Y-m-d H:i:s');
						$query = new AllowanceConfigs();
						$query->fromArray($new);
						$query->save();

						if( ! $query->trySave() ) {
							$this->_helper->_FlashMessenger->addMessage(array('error' => "New Allowance Feature not saved."));
						}
						else {
							$this->_helper->_FlashMessenger->addMessage(array('notice' => "New Allowance Feature added.", 'redirect' => '/allowances/allowance/id/'.$allowance->{0}->id.'/'));
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
	 *	Is Owner
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _isOwner($id = null) {
		return ($id == $this->user->id) ? true:false;
	}
	// END

	/**
	 *	User Has Settings
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _userHasSettings() {
		$query = Configurations::getConfigurationsByUserId($this->user->id);
		if( ! empty($query) ) {
			foreach($query as $row) {
				if( $row['type_id'] == Core_Pay::BASE_TYPE_ID ) {
					return true;
				}
			}
		}
		return false;
	}
	// END

	/**
	 *	User Has Allowances
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _userHasAllowances() {
		$query = Allowances::getAllowancesByUserId($this->user->id);
		return ( empty($query) ) ? false:true;
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

}
// END CLASS


// EOF