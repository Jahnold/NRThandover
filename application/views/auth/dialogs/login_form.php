<?php
$email = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'value' => set_value('login'),
	'maxlength'	=> 80,
	'size'	=> 30,
    'class' => 'form-control',
    'label' => 'Email Address'
);
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30,
    'class' => 'form-control',
    'label' => 'Password'
);
$remember = array(
	'name'	=> 'remember',
	'id'	=> 'remember',
	'value'	=> 1,
	'checked'	=> set_value('remember'),
);
$submit = array(
    'type'  => 'submit',
    'content'   => 'Sign In',
    'class' => 'btn btn-primary',
    'id'    => 'sign-in-button'
);
$modal = array(
    'id'    => 'sign-in-dialog',
    'title'  => 'Sign In'
);
$form = array(
    'id'    => 'login-form'
);

$this->load->view('bootstrap/modal_start', $modal); // begin the modal

echo form_open(base_url('auth/login'),$form);

echo tbs_form_input($email);
echo "<p id='email-error'></p>";
echo tbs_form_password($password);
echo "<p id='password-error'></p>";
echo tbs_form_checkbox($remember,'Remember Me');

$this->load->view('bootstrap/modal_middle'); // modal middle

echo anchor('/auth/forgot_password/', 'Forgotten Password?', 'id="forgot-password"');
echo form_button($submit);
echo form_close();

$this->load->view('bootstrap/modal_end'); // end the modal
?>
