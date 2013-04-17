<?php 

class ConfigurationsController extends Zend_Controller_Action {

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

		$this->view->headTitle()->prepend('Settings');
	}
	// END

	/**
	 *	ACTION = Index
	 */
	public function indexAction() {}
	// END

	/**
	 *	Proxy = Settings
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function allowancesettingsAction() {

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$id = $this->_getParam('id');
		$id = ( empty($id) ) ? $this->user->id:$id;

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$configurations = Configurations::getConfigurationsByUserId($id);

		/* ------------------------------ *
		 *	PROCESS
		 * ===============================
		 *	Configurations
		 * ------------------------------ */
		$this->data->userHasSettings = ( empty($configurations) ) ? false:true;

		//Core_P::p($configs, 1);

		/* ------------------------------ *
		 *	PROCESS
		 * ===============================
		 *	Configurations
		 * ------------------------------ */
		$this->data = Core_Helper::compile_data($this->data, $configurations, 'configurations');

		/* ------------------------------ *
		 * 	OUTPUT
		 * ------------------------------ */
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	Proxy = Setting
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function allowancesettingAction() {

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$id 	= $this->_getParam('id');
		$owner 	= ( empty($id) ) ? true:false;

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
		} elseif( ! empty($configuration->{0}->id) ) {
			$owner = $this->_isOwner($configuration->{0}->user_id);
			if( false === $owner && $this->user->role != Core_Acl_Roles::ADMINS ) {
				throw new Zend_Exception("You are not authorized to view this record");
			}
		}

		/* ------------------------------ *
		 *	PROCESS
		 * ===============================
		 *	Configuration
		 * ------------------------------ */
		$this->_configuration_form($configuration, $owner);

		/* ------------------------------ *
		 * 	OUTPUT
		 * ------------------------------ */
		$this->view->data = $this->data;
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

		/* ------------------------------ *
		 * 	FORM
		 * ------------------------------ */
		$this->_concierge_form();

		/* ------------------------------ *
		 * 	OUTPUT
		 * ------------------------------ */
		$this->view->data = $this->data;
	}
	// END


/*
 * ===========================
 *	Private functions
 * ===========================
 */

	/**
	 *	Configuration Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _configuration_form($configuration, $owner = false) {

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
				$data['user_id'] = ( $owner ) ? $this->user->id:$configuration->{0}->user_id;
				//Core_P::p($data, 1);

				$configuration->{0}->fromArray($data);
				//Core_P::p($configuration->{0}->toArray(), 1);

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
						$this->_helper->_FlashMessenger->addMessage(array('notice' => "New Setting saved.", 'redirect' => '/allowances/setting/id/'.$id.'/'));
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

	/**
	 *	Concierge Build Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _concierge_form() {

		$form = new Form_Concierge();

		//Core_P::p($allowance->toArray(), 1);

		/* ------------------------------ *
		 * 	ROUTE POST
		 * ------------------------------ */
		if( $this->getRequest()->isPost() ) {

			$post 	= $this->_request->getPost();
			$values = $form->populate($post)->getValues();

			//Core_P::p($values, 1);

			/**
			 * 	Valid Post
			 */
			if( $form->isValid($post) ) {

				/**
				 * 	Build Setting
				 */
				try {
					$configuration = new Configurations();
					$data = array(
									'type_id' 	=> Core_Pay::BASE_TYPE_ID,
									'user_id' 	=> $this->data->user->id,
									'action' 	=> Core_Pay::ACTION_EARNINGS,
									'label' 	=> "Allowance Base"
								);
					$configuration->fromArray($data);
					$configuration->save();
	
					$config_id = $configuration->id;
				} catch(Exception $e) {
					Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
				}

				/**
				 * 	Build Allowance
				 */
				try {
					$allowance = new Allowances();
					$data = array(
									'user_id' 	=> $this->data->user->id,
									'label' 	=> "J. Doe",
									'period' 	=> Core_Interval::WEEKLY,
									'created' 	=> date('Y-m-d H:i:s'),
									'author_id' => $this->data->user->id,
									'active' 	=> 1
								);
					$allowance->fromArray($data);
					$allowance->save();
	
					$allowance_id = $allowance->id;
				} catch(Exception $e) {
					Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
				}

				/**
				 * 	Build Allowance
				 */
				try {

					$allowance_config = new AllowanceConfigs();

					$data = array(
									'config_id' 	=> $config_id,
									'allowance_id' 	=> $allowance_id,
									'amount' 		=> "10.00",
									'active' 		=> 1
								);
					$allowance_config->fromArray($data);
					$allowance_config->save();

				} catch(Exception $e) {
					Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
				}

				if( empty($allowance_id) ) {
					$this->_helper->_FlashMessenger->addMessage(array('error' => "Concierge Build not completed."));
				}
				else {	
					$this->_helper->_FlashMessenger->addMessage(array('notice' => "Concierege Build completed.", 'redirect' => '/allowances/allowance/id/'.$allowance_id.'/'));
				}
			}
			else {
				$this->_helper->_FlashMessenger->addMessage(array('error' => "Errors."));
			}
		}

		$this->data->concierge->form = $form;
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
	 *	User Has Configurations
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _userHasConfigurations() {
		$query = Configurations::getConfigurationsByUserId($this->user->id);
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