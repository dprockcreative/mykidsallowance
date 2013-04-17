<?php

class AccountController extends Zend_Controller_Action {

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
		$this->data->activity 	= Users::getActivity($this->user->id);

		$this->_helper->getHelper('FlashMessenger');

		$this->view->headTitle()->prepend('Account');
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
		$id = ( empty($id) ) ? $this->user->id:$id;

		/* ------------------------------ *
		 * 	MODEL
		 * ------------------------------ */
		$account = Users::getUserById($id);

		$this->_account = $account[0];
		//Core_P::p($this->_account, 1);

		$params = array(
							'to' 		=> 'd_prock@speakeasy.net', 
							'sender' 	=> 'bugbear', 
							'subject' 	=> 'Profile Updated', 
							'body' 		=> 'test'
						);

		Core_Email::send($params);

		/* ------------------------------ *
		 * 	VALIDATE
		 * ------------------------------ */
		if( false === $this->_isOwner() && $this->user->role != Core_Acl_Roles::ADMINS ) {
			$this->_helper->_FlashMessenger->addMessage(array('error' => "Users may only edit their own accounts."));
			$this->data->account->form = null;
		} else {
			$this->_profile_form($id);
		}


		$this->view->headTitle()->prepend('Profile');
		$this->view->headTitle()->prepend($this->_account['username']);

		/* ------------------------------ *
		 * 	OUTPUT
		 * ------------------------------ */
		$this->view->data = $this->data;
	}
	// END

	/**
	 *	ACTION = Login
	 *
	 * 	@Proxies to Auth Controller, which inherits Auth & ACL
	 */
	public function loginAction() {
		$this->_helper->layout->disableLayout();
		$this->_forward('login', 'auth', null, array('data' => $this->data));
	}
	// END

	/**
	 *	ACTION = Resend
	 *
	 * 	@Redirects to Auth Controller, which inherits Auth & ACL
	 */
	public function resendAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->redirector->goto('resend', 'auth', null);
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
	 *	Profile Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _profile_form($id = 0) {

		$form = new Form_Profile();
		$form->setUser($this->user);
		$form->setAccount($this->_account);
		$form->setAdminParams();
		$form->addValidators();

		/* ------------------------------ *
		 * 	ROUTE POST
		 * ------------------------------ */
		if( $this->getRequest()->isPost() ) {

			$post 	= $this->_request->getPost();
			$values = $form->populate($post)->getValues();
			$values = $form->cleanValues($values);

			/**
			 * 	Valid Post
			 */
			if( $form->isValid($post) ) {
				//Core_P::p($values, 1);

				$id = $this->_account['id'];

				$values['updated'] = date("Y-m-d H:i:s", time());

				if( false !== Users::saveUser($id, $values) ) {

					/**
					 * 	Admin is User && Password change ? :: log out
					 */
					if( $this->_isOwner() && ! empty($values['password']) && ( $values['password'] != $post['authenticatepassword'] ) ) {
						$this->_helper->_FlashMessenger->addMessage(array('notice' => "Password changed. Logging out."));
						$this->_redirect('/auth/logout/');
						exit();
					}

					$this->_helper->_FlashMessenger->addMessage(array('notice' => "Account updated."));

					if( $this->_isOwner() ) {
						$params = array(
											'to' 		=> $values['Members']['email'], 
											'sender' 	=> $values['screenname'], 
											'subject' 	=> 'Profile Updated', 
											'body' 		=> '-t-profile-updated'
										);
				
						Core_Email::send($params);
					}
				}
				else {
					$this->_helper->_FlashMessenger->addMessage(array('error' => "Account not updated."));
				}
			}
			else {
				$this->_helper->_FlashMessenger->addMessage(array('error' => "Errors."));
			}
		} else {
			$pop = $this->_account;
			$form->populate($pop);
		}

		$this->data->account->form = $form;
	}
	// END

	/**
	 *	Is Owner
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _isOwner() {
		return ($this->_account['id'] == $this->user->id) ? true:false;
	}
	// END

}
// END CLASS