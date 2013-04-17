<?php 

class Core_Email {

	protected $_config;

	public function __construct() {
		$this->_config = Zend_Registry::get('config');
	}

	/**
	 *	Send
	 *
	 *	@access	private
	 *	@param	void
	 *	@return	void
	 */
	public function send($params = array()) {

		if( empty($this->_config) ) {
			$this->_config = Zend_Registry::get('config');
		}

		$to 		= $params['to'];
		$sender 	= $params['sender'];
		$subject 	= $params['subject'];
		$body 		= $params['body'];

		if( substr($body, 0, 3) == '-t-') {

			$this->data->email = $params;

			$view 		= new Zend_View();
			$view->setScriptPath(APPLICATION_PATH.'/views/helpers/templates/email/');
			$template 	= substr($body, 3).'.tpl';
			$body 		= (string) $view->assign((array) $this->data)->render($template);
		}

		//Core_P::p($body, 1);
		return true;

		$auth = array('auth' => 'login', 'username' => 'dp%dprockcreative.com', 'password' => 'para505');

		$tr = new Zend_Mail_Transport_Smtp($this->_config->site->default->email->smtp->domain, $auth);
		Zend_Mail::setDefaultTransport($tr);

		$mail = new Zend_Mail();
		$mail->setFrom($this->_config->site->default->email->from->address, $this->_config->site->default->email->from->sender)
			->addTo($to, $sender)
			->setSubject($subject)
			->setBodyText($body)
			->send();

		return true;
	}
	// END
}
// END CLASS

