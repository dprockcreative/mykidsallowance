<?php 
	//print_r($this);die;
?>
Dear <?= $this->user->screenname; ?>,

This email is a response to a request for an Account Re-activation Email to be resent. If this request was not made by you, please ignore.

To re-activate your account click here:

<?= $this->config->site->default->url; ?>/auth/confirm?uid=<?= $this->unique_id; ?>


=====================
The My Allowance Team