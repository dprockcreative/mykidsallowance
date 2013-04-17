<?php 

/**
 *	Auth Controller
 */
class AuthController extends Zend_Controller_Action {

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

		$this->_helper->getHelper('FlashMessenger');

	}
	// END

/*
 * ===========================
 *	Actions
 * ===========================
 */

	/**
	 *	ACTION = Index
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function indexAction() {
		$this->_helper->layout->disableLayout();
		$this->_redirect('/account');
		exit;
	}
	// END

	/**
	 *	ACTION = LOG IN
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function loginAction() {

		$this->view->headTitle()->prepend('Sign Up');
		$this->view->headTitle()->prepend('Log In');

		$this->_login_form();
		$this->_forgot_form();
		$this->_signup_form();
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = Reset Password
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function resetpasswordAction() {

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$unique_id = $this->_getParam('uid');

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$user = Users::getUserByUniqueId($unique_id);

		/* ------------------------------ *
		 * 	VALIDATE
		 * ------------------------------ */
		if( ! empty($this->user->id) ) {
			throw new Zend_Exception("You are already logged in.");
		}
		else if( empty($user->{0}->id) ) {
			throw new Zend_Exception("Invalid Parameters.");
		} else {
			$this->_resetpassword_form($user);
		}

		//Core_P::p($user->toArray(), 1);


		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = CONFIRM
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function confirmAction() {

		/* ------------------------------ *
		 * 	DEFAULTS
		 * ------------------------------ */
		$msg = '';

		/* ------------------------------ *
		 * 	PARAMS
		 * ------------------------------ */
		$unique_id = $this->_getParam('uid');

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$user = Users::getUserByUniqueId($unique_id);
	
		$q = $user->{0};

		if( Core_Acl_Roles::GUESTS != Core_Acl_Roles::getRoleFromGroupId($q->group_id) ) {
			$msg = 'User already confirmed.';
			$this->_helper->_FlashMessenger->addMessage(array('error' => $msg));
		} else {
			$q->group_id 	= Core_Acl_Roles::getGroupIdFromRole(Core_Acl_Roles::USERS);
			$q->updated 	= date('Y-m-d H:i:s');
			$q->editor_id 	= $q->id;
			$q->active 		= 1;
			$user->save();
			$msg = 'User email is confirmed.';
			$this->_helper->_FlashMessenger->addMessage(array('notice' => $msg));
		}
		$this->data->msg 	= $msg;
		$this->view->data 	= $this->data;
	}
	// END

	/**
	 *	ACTION = RESEND
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function resendAction() {
		$this->_helper->layout->disableLayout();

		$email = Members::getEmailById($this->user->id);

		if( empty($email) ) {
			exit("Invalid Parameters");
		}

		$this->data->unique_id = Users::getUniqueIdById($this->user->id);
		$params = array(
							'to' 		=> $email, 
							'sender' 	=> $this->user->screenname, 
							'subject' 	=> 'Activation Resent', 
							'body' 		=> '-t-resend-activation'
						);
		if( Core_Email::send($params) ) {
			exit("Activation Email Sent.");
		} 
		else {
			exit("Email problems please try again later.");
		}

	}
	// END

	/**
	 *	ACTION = REACTIVATE
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function reactivateAction() {
		$this->_helper->layout->disableLayout();

		$email = Members::getEmailById($this->user->id);

		if( empty($email) ) {
			exit("Invalid Parameters");
		}

		$this->data->unique_id = Users::getUniqueIdById($this->user->id);
		$params = array(
							'to' 		=> $email, 
							'sender' 	=> $this->user->screenname, 
							'subject' 	=> 'Re-activation Sent', 
							'body' 		=> '-t-resend-activation'
						);
		if( Core_Email::send($params) ) {
			exit("Re-activation Email Sent.");
		} 
		else {
			exit("Email problems please try again later.");
		}

	}
	// END

	/**
	 *	ACTION = LOG OUT
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function logoutAction() {
		$this->_helper->layout->disableLayout();

		$auth = Zend_Auth::getInstance();
		$user = $auth->getIdentity();Core_P::p($user);
		Sessions::closeSession($user->session_id);
		$auth->clearIdentity();
		$this->_helper->_FlashMessenger->addMessage(array('notice' => "Logged out."));
		$this->_redirect('/account/');
		exit;
	}
	// END

	/**
	 *	ACTION = ACTIVITY
	 *
	 *	@access	public
	 *	@param	void
	 *	@return	void
	 */
	public function activityAction($data) {
		$this->data = $data;
		$this->data->activity = Users::getActivity($this->user->id);
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
	 *	Login Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _login_form() {

		$form = new Form_Login();

		//Core_P::p($this->user);

		/* ------------------------------ *
		 * 	ROUTE POST
		 * ------------------------------ */
		if( $this->getRequest()->isPost() && $this->_request->getPost('_login') ) {

			$post 	= $this->_request->getPost();
			$values = $form->populate($post)->getValues();

			/**
			 * 	Valid Post
			 */
			if( $form->isValid($post) ) {

				$adapter = $this->_adapter($values['username'], $values['password']);

				if( $adapter['code'] != 1 ) {
					$this->_helper->_FlashMessenger->addMessage(array('error' => $adapter['messages'][0]));
					$form->markAsError();
				} else {

					if( $this->user->role == Core_Acl_Roles::GUESTS && empty($this->user->active) ) {
						$this->_helper->_FlashMessenger->addMessage(array('error' => "User not yet Confirmed"));
						$form->markAsError();
						$form->logingrp->setDescription('<a href="/auth/resend/" id="resend_activation">Resend Activation Email</a>');
					} 
					else if( $this->user->role != Core_Acl_Roles::GUESTS && empty($this->user->active) ) {
						$this->_helper->_FlashMessenger->addMessage(array('error' => "Membership Inactivated"));
						$form->markAsError();
						$form->logingrp->setDescription('<a href="/auth/reactivate/" id="resend_activation">Send Re-activation Email</a>');
					} 
					else {

						$this->_helper->_FlashMessenger->addMessage(array('error' => "Logged in."));
	
						$osd = new Zend_Session_Namespace('sd');
						if( isset($osd->sd) ) {
							$_module 		= $osd->sd->_module;
							$_controller 	= $osd->sd->_controller;
							$_action 		= $osd->sd->_action;
							if( $_module != null && $_controller != null && $_action != null) {
								if( $osd->sd->_params ) {
									$this->_helper->redirector->goto($_action, $_controller, $_module, $osd->sd->_params);
								}
								else {
									$this->_helper->redirector->goto($_action, $_controller, $_module);
								}
							}
						}
						$this->_redirect("/");
					}
				}
			}
			else {
				$this->_helper->_FlashMessenger->addMessage(array('error' => "Errors."));
			}
		} 

		$this->data->login->form = $form;
	}
	// END

	/**
	 *	Forgot Password Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _forgot_form() {

		$form = new Form_Forgot();

		/* ------------------------------ *
		 * 	ROUTE POST
		 * ------------------------------ */
		if( $this->getRequest()->isPost() && $this->_request->getPost('_forgot') ) {

			$post 	= $this->_request->getPost();
			$values = $form->populate($post)->getValues();

			/**
			 * 	Valid Post
			 */
			if( $form->isValid($post) ) {
				//Core_P::p($values, 1);

				$user = Users::getUserFromEmail($values['email']);

				if( count($user[0]) > 1 ) {
					//Core_P::p($user, 1);
					$this->data->resetpassword = Core_Helper::to_obj($user[0], false);

					$params = array(
										'to' 		=> $this->data->resetpassword->Members->email, 
										'sender' 	=> $this->data->resetpassword->screenname, 
										'subject' 	=> 'Reset Password', 
										'body' 		=> '-t-reset-password'
									);
					Core_Email::send($params);

					$this->_helper->_FlashMessenger->addMessage(array('notice' => "Email Sent.", 'redirect' => '/account/'));

				}
				else {
					$this->_helper->_FlashMessenger->addMessage(array('error' => "Email not sent."));
				}
			}
			else {
				$this->_helper->_FlashMessenger->addMessage(array('error' => "Errors."));
			}
		} 

		$this->data->forgot->form = $form;
	}
	// END

	/**
	 *	Reset Password Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _resetpassword_form($user) {

		$form = new Form_ResetPassword();

		/* ------------------------------ *
		 * 	ROUTE POST
		 * ------------------------------ */
		if( $this->getRequest()->isPost() ) {

			$post 	= $this->_request->getPost();
			$values = $form->populate($post)->getValues();

			/**
			 * 	Valid Post
			 */
			if( $form->isValid($post) ) {
				//Core_P::p($values, 1);

				$data = array(
								'unique_id' => md5(time()),
								'updated' 	=> date('Y-m-d H:i:s')
							);

				$data = $values+$data;
				$user->{0}->fromArray($data);
				$user->save();

				if(  ! $user->trySave() ) {
					$this->_helper->_FlashMessenger->addMessage(array('error' => "New password not saved."));
				}
				else {
					$this->_helper->_FlashMessenger->addMessage(array('notice' => "New password saved.", 'redirect' => '/account/'));
				}
			}
			else {
				$this->_helper->_FlashMessenger->addMessage(array('error' => "Errors."));
			}
		} 

		$this->data->resetpassword->form = $form;
	}
	// END

	/**
	 *	Signup Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _signup_form() {

		$form = new Form_Signup();

		/* ------------------------------ *
		 * 	ROUTE POST
		 * ------------------------------ */
		if( $this->getRequest()->isPost() && $this->_request->getPost('_signup') ) {

			$post 	= $this->_request->getPost();
			$values = $form->populate($post)->getValues();

			/**
			 * 	Valid Post
			 */
			if( $form->isValid($post) ) {
				$data 	= array(
									'group_id' 	=> Core_Acl_Roles::getGroupIdFromRole(Core_Acl_Roles::GUESTS),
									'unique_id' => md5(time()),
									'created' 	=> date('Y-m-d H:i:s')
								);
				$data 	= $values+$data;
				$id 	= Users::saveUser("", $data);

				if( ! empty($id) ) {
					$this->data->unique_id = $data['unique_id'];
					$params = array(
										'to' 		=> $values['Members']['email'], 
										'sender' 	=> $values['screenname'], 
										'subject' 	=> 'Profile Updated', 
										'body' 		=> '-t-signup-complete'
									);
					Core_Email::send($params);
				}
				$this->_helper->_FlashMessenger->addMessage(array('notice' => "Success. Please check your email."));
			}
			else {
				$this->_helper->_FlashMessenger->addMessage(array('error' => "Errors."));
			}
		}
		$this->data->signup->form = $form;
	}
	// END

	/**
	 *	Adapter
	 *
	 *	@access	private
	 *	@param	string
	 *	@param	string
	 *	@return	array
	 */
	private function _adapter($username, $password) {

		$response = array('code' => null, 'messages' => null);

		try {

			$adapter = new Zend_Auth_Adapter_Doctrine_Table( Doctrine::getConnectionByTableName('Users') );	
			$adapter->setTableName('Users') 
					->setIdentityColumn('username') 
					->setCredentialColumn('password') 
					->setCredentialTreatment('md5(?)') 
					->setIdentity($username) 
					->setCredential($password);

			$auth 	= Zend_Auth::getInstance(); 
			$result = $auth->authenticate($adapter);

			$response['code'] 		= $result->getCode();
			$response['messages'] 	= $result->getMessages();

			if($result->isValid()) { 

				$data = $adapter->getResultRowObject(null, array('unique_id', 'password'));
				$data->lastlogin = date("Y-m-d H:i:s", time());

				$data = $this->_saveLogin($data);

				if( empty($data->active) ) {
					$data->role = Core_Acl_Roles::GUESTS;
					$data->group_id = Core_Acl_Roles::getGroupIdFromRole($data->role);
				}

				$auth->getStorage()->write($data);

				$this->user 		= $auth->getIdentity();
				$this->data->user 	= $this->user;

			} 
			else {
				Core_Logger::getInstance()->err(__METHOD__ . " - " . print_r($result, 1));
			}
		} catch(Exception $e) {
			Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
			throw new Zend_Exception($e);
		}

		return $response;
	}
	// END

	/**
	 *	Save Login
	 *
	 *	@access	private
	 *	@param	object
	 *	@return	object
	 */
	private function _saveLogin($data = null) {

		$data->role 			= Core_Acl_Roles::getRoleFromGroupId($data->group_id);
		$data->session_id 		= sha1(uniqid());
		$data->ip_address 		= $this->ip;
		$data->user_agent 		= ( isset($_SERVER['HTTP_USER_AGENT']) ) ? $_SERVER['HTTP_USER_AGENT']:'';
		$data->last_activity 	= time();

		if( $data->role == Core_Acl_Roles::GUESTS || empty($data->active) ) { 
			$data->active = 0;
			return $data;
		}

		$user_data 	= (array) $data;
		$session 	= array(
							'session_id' 	=> $data->session_id,
							'user_id' 		=> $data->id,
							'ip_address' 	=> $data->ip_address,
							'user_agent' 	=> $data->user_agent,
							'last_activity' => $data->last_activity,
							'user_data' 	=> (count($user_data) > 0) ? serialize($user_data):''
						);

		Sessions::saveSession($session);

		Users::saveUser($data->id, array('lastlogin' => date('Y-m-d H:i:s')));

		return $data;
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
}
// END CLASS