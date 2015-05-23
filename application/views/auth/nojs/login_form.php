<?php
$email = array(
    'name'	=> 'login',
    'id'	=> 'login',
    'value' => set_value('login'),
    'maxlength'	=> 80,
    'size'	=> 30,
    'class' => form_error('login') !== '' ? 'form-control error' : 'form-control',   // if there is an error add the error class
    'label' => 'Email Address'
);
$password = array(
    'name'	=> 'password',
    'id'	=> 'password',
    'size'	=> 30,
    'class' => form_error('password') !== '' ? 'form-control error' : 'form-control',   // if there is an error add the error class
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
$register = array(
    'type'  => 'button',
    'content'   => 'Sign Up',
    'class' => 'btn btn-default',
    'id'    => 'sign-up-button'
);
$form = array(
    'id'    => 'login-form'
);
?>
<div class="row">
<div class="col-md-6 col-md-offset-3">
<p>&nbsp;</p>
<?php

echo form_open(base_url('auth/login'),$form);

echo tbs_form_input($email);
echo form_error('login', '<p id="login-error">','</p>');

echo tbs_form_password($password);
echo form_error('password', '<p id="password-error">','</p>');

echo tbs_form_checkbox($remember,'Remember Me');

echo "<div class='pull-right'>";
echo anchor('/auth/forgot_password/', 'Forgotten Password?', 'id="forgot-password"');
echo form_button($submit);
echo "</div>";

echo form_close();

?>
</div>
</div>