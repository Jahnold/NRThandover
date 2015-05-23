<?php
$first_name = array(
    'name'  => 'first_name',
    'id'    => 'first_name',
    'value' => set_value('first_name'),
    'class' => 'form-control',
    'label' => 'First Name'
);
$last_name = array(
    'name'  => 'last_name',
    'id'    => 'last_name',
    'value' => set_value('last_name'),
    'class' => 'form-control',
    'label' => 'Last Name'
);
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
    'class' => 'form-control',
    'label' => 'Email Address'
);
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'value' => set_value('password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
    'class' => 'form-control',
    'label' => 'Password'
);
$confirm_password = array(
	'name'	=> 'confirm_password',
	'id'	=> 'confirm_password',
	'value' => set_value('confirm_password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
    'class' => 'form-control',
    'label' => 'Confirm Password'
);
$submit = array(
    'type'  => 'submit',
    'content'   => 'Sign Up',
    'class' => 'btn btn-primary'
);
$modal = array(
    'id'    => 'sign-up-dialog',
    'title'  => 'Sign Up'
);

$this->load->view('bootstrap/modal_start', $modal); // begin the modal

echo form_open(base_url('auth/register'));
echo tbs_form_input($first_name);
echo tbs_form_input($last_name);
echo tbs_form_input($email);
echo tbs_form_password($password);
echo tbs_form_password($confirm_password);

$this->load->view('bootstrap/modal_middle'); // modal middle

echo form_button($submit);
echo form_close();

$this->load->view('bootstrap/modal_end'); // end the modal
?>