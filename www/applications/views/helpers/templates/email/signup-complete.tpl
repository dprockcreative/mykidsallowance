<?php 
	//print_r($this);die;
?>
Dear <?= $this->email['sender']; ?>,

Your account was created but to activate you must click here:

<?= $this->config->site->default->url; ?>/auth/confirm?uid=<?= $this->unique_id; ?>


=====================
The My Allowance Team