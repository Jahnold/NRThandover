<?php
$email = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'value' => set_value('login'),
	'maxlength'	=> 80,
    'class' => 'form-control',
    'label' => 'Email Address'
);
$submit = array(
    'type'  => 'submit',
    'content'   => 'Request Password Reset',
    'class' => 'btn btn-primary'
);
$cancel = array(
    'type'  => 'button',
    'content'   => 'Cancel',
    'class' => 'btn btn-default',
    'id'    => 'cancel-password-reset-button'
);
$modal = array(
    'id'    => 'new-password-dialog',
    'title'  => 'Request Password Reset'
);

$this->load->view('bootstrap/modal_start', $modal); // begin the modal

echo form_open(base_url('auth/forgot_password'));
echo tbs_form_input($email);

$this->load->view('bootstrap/modal_middle'); // modal middle

echo form_button($cancel);
echo form_button($submit);
echo form_close();

$this->load->view('bootstrap/modal_end'); // end the modal
?>