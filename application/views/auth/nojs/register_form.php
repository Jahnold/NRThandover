<?php
$first_name = array(
    'name'  => 'first_name',
    'id'    => 'first_name',
    'value' => set_value('first_name'),
    'class' => form_error('first_name') !== '' ? 'form-control error' : 'form-control',   // if there is an error add the error class
    'label' => 'First Name'
);
$last_name = array(
    'name'  => 'last_name',
    'id'    => 'last_name',
    'value' => set_value('last_name'),
    'class' => form_error('last_name') !== '' ? 'form-control error' : 'form-control',
    'label' => 'Last Name'
);
$email = array(
    'name'	=> 'email',
    'id'	=> 'email',
    'value'	=> set_value('email'),
    'maxlength'	=> 80,
    'class' => form_error('email') !== '' ? 'form-control error' : 'form-control',
    'label' => 'Email Address'
);
$password = array(
    'name'	=> 'password',
    'id'	=> 'password',
    'value' => set_value('password'),
    'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
    'class' => form_error('password') !== '' ? 'form-control error' : 'form-control',
    'label' => 'Password'
);
$confirm_password = array(
    'name'	=> 'confirm_password',
    'id'	=> 'confirm_password',
    'value' => set_value('confirm_password'),
    'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
    'class' => form_error('confirm_password') !== '' ? 'form-control error' : 'form-control',
    'label' => 'Confirm Password'
);
$submit = array(
    'type'  => 'submit',
    'content'   => 'Sign Up',
    'class' => 'btn btn-primary pull-right'
);
$modal = array(
    'id'    => 'sign-up-dialog',
    'title'  => 'Sign Up'
);
?>
<div class="row">
<div class="col-md-6 col-md-offset-3">
<p>&nbsp;</p>
<h1>Sign Up</h1>
<?php
echo form_open(base_url('auth/register'));

echo tbs_form_input($first_name);
echo form_error('first_name', '<p class="error">','</p>');

echo tbs_form_input($last_name);
echo form_error('last_name', '<p class="error">','</p>');

echo tbs_form_input($email);
echo form_error('email', '<p class="error">','</p>');

echo tbs_form_password($password);
echo form_error('password', '<p class="error">','</p>');

echo tbs_form_password($confirm_password);
echo form_error('confirm_password', '<p class="error">','</p>');

echo form_button($submit);

echo form_close();

?>
</div>
</div>