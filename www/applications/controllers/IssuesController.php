<?php 

class IssuesController extends Zend_Controller_Action {

	public $data;
	public $config;

	/**
	 *	Init
	 */
	public function init() {
		$this->_helper->layout->setLayout('layout');	
		$this->data 			= new stdClass;
		$this->data->config 	= Zend_Registry::get('config');

		$auth 					= Zend_Auth::getInstance();
		$this->user 			= $auth->getIdentity();
		$this->data->user 		= $this->user;

		$this->_helper->layout->disableLayout();
	}
	// END


	/**
	 *	ACTION = Index
	 */
	public function indexAction() {

		/**
		 * 	Detect Ajax Request
		 */
		if( ! isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
			$this->_redirect('/');
		}

		/**
		 * 	Request
		 */
		$request = array(
							'referer' 	=> ( isset($_SERVER['HTTP_REFERER']) ) ? Core_Helper::setUrlString($_SERVER['HTTP_REFERER']):null,
							'uri' 		=> $this->getRequest()->getRequestUri()
						);

		$is_admin = ($this->user->role == Core_Acl_Roles::ADMINS) ? true:false;

		/**
		 * 	Query
		 */
		$issues = Issues::getIssuesByReferer($request['referer'], $is_admin);

		$this->data = Core_Helper::compile_data($this->data, $request, 'request');
		$this->data = Core_Helper::compile_data($this->data, $issues, 'issues');

		/**
		 * 	Add Admin Forms
		 */
		if( $is_admin ) {
			foreach($this->data->issues as $issue) {

				$issue->{0}->form = $this->_update_issue_form($issue->{0});

				foreach($issue->{0}->IssueComments as $comment) {
					$comment->form = $this->_update_comment_form($comment);
				}
			}
		}
		//Core_P::p($this->data->issues, 1);

		/**
		 * 	Form
		 */
		$this->_issue_form();
		$this->_comment_form();

		/**
		 * 	Output
		 */
		$output = $this->view->assign((array) $this->data)->render('issues/index.phtml');
		exit($output);
	}
	// END

	/**
	 *	ACTION = Update Issue
	 */
	public function updateissueAction() {

		/**
		 * 	Params
		 */
		$id = $this->_getParam('id');

		/**
		 * 	Query
		 */
		$update = Issues::updateIssue($id, $_POST);

		/**
		 * 	Set Response
		 */
		$response = ( empty($update) ) ? 'false':true;

		exit($response);
	}
	// END

	/**
	 *	ACTION = Update Issue Comment
	 */
	public function updateissuecommentAction() {

		/**
		 * 	Params
		 */
		$id = $this->_getParam('id');

		/**
		 * 	Query
		 */
		$update = IssueComments::updateComment($id, $_POST);

		/**
		 * 	Set Response
		 */
		$response = ( empty($update) ) ? 'false':true;

		exit($response);
	}
	// END

	/**
	 *	ACTION = Add Comment
	 */
	public function addcommentAction() {

		/**
		 * 	Detect Ajax Request
		 */
		if( ! isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
			$this->_redirect('/');
		}

		/**
		 * 	Params
		 */
		$issue_id = $this->_getParam('issue_id');

		/**
		 * 	Form
		 */
		$flag = $this->_comment_form($issue_id);

		/**
		 * 	Output
		 */
		$form = $this->view->assign((array) $this->data)->render('issues/addcomment.phtml');

		$response = array(
							'result' 	=> $flag,
							'form' 		=> $form
						);

		$output = Zend_Json::encode($response);

		exit($output);
	}
	// END

	/**
	 *	ACTION = Get Comment
	 */
	public function getcommentAction() {

		/**
		 * 	Detect Ajax Request
		 */
		if( ! isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
			$this->_redirect('/');
		}

		/**
		 * 	Params
		 */
		$issue_id = $this->_getParam('issue_id');

		/**
		 * 	Query
		 */
		$comment = IssueComments::getLastByIssueId($issue_id);

		$this->data = Core_Helper::compile_data($this->data, $comment, 'comment');

		/**
		 * 	Output
		 */
		$output = $this->view->assign((array) $this->data)->render('issues/getcomment.phtml');
		exit($output);
	}
	// END


/*
 * ===========================
 *	Private functions
 * ===========================
 */


	/**
	 *	Issue Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _issue_form() {

		$form = new Form_Issue();

		$form->setUser($this->user);

		$message 	= array();

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

				$data = array(
								'referer' 	=> $this->data->request->referer,
								'timestamp' => time()
							);
				$data = $values+$data;

				$issue = new Issues();

				$issue->fromArray($data);

				if( ! $issue->trySave() ) {
					$message = array('error' => "Issue not saved.");
				}
				else {
					$message = array('notice' => "Saving Issue.");
				}
			}
		}

		if( count($message) > 0 ) {
			$form->setMessage($message, $this->data->request->referer);
		}

		$this->data->tracker->form = $form;
	}
	// END

	/**
	 *	Issue Comment Form
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	private function _comment_form($issue_id = 0) {

		$form = new Form_IssueComment();

		$form->setUser($this->user);
		$form->setIssueId($issue_id);

		$message 	= array();
		$flag 		= false;

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

				$data = array(
								'issue_id' 	=> $issue_id,
								'timestamp' => time()
							);
				$data = $values+$data;

				//Core_P::p($data, 1);

				$comment = new IssueComments();
				$comment->fromArray($data);

				if( ! $comment->trySave() ) {
					$message= array('error' => "Comment not saved.");
				}
				else {
					$flag = true;
				}
			} else {
				//$message = array('error' => "post: ".print_r($post, 1));
			}
		}

		if( count($message) > 0 ) {
			$form->setMessage($message);
		}

		$this->data->comment->form = $form;

		return $flag;
	}
	// END

	/**
	 *	Update Issue Form
	 *
	 *	@access	private
	 *	@param	object
	 *	@return	string
	 */
	private function _update_issue_form($issue) {

//Core_P::p($issue, 1);

		$form = new Form_AdminIssue();
		$form->setId($issue->id);
		$form->populate((array) $issue);
		return $form->render();
	}
	// END

	/**
	 *	Update Comment Form
	 *
	 *	@access	private
	 *	@param	object
	 *	@return	string
	 */
	private function _update_comment_form($comment) {
		$form = new Form_AdminIssueComment();
		$form->setId($comment->id);
		$form->populate((array) $comment);
		return $form->render();
	}
	// END

}
// END CLASS


// EOF