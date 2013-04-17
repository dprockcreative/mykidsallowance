<?php 
	//print_r($this->config);die;
?>
Dear <?= $this->resetpassword->screenname; ?>,

This email is a response to a request for password reset on your account. If this request was not made by you, please ignore.

Otherwise click this one-time-use link to change your Password.

<?= $this->config->site->default->url; ?>/auth/resetpassword/uid/<?= $this->resetpassword->unique_id; ?>/


=====================
The My Allowance Team