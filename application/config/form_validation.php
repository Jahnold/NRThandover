<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Matthew
 * Date: 12/02/14
 * Time: 22:29
 * To change this template use File | Settings | File Templates.
 */
$config = array(
    'login' => array(
        array(
            'field' => 'login',
            'label' => 'Email Address',
            'rules' =>  'trim|required|valid_email|xss_clean'
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' =>  'trim|required|xss_clean'
        ),
        array(
            'field' => 'remember',
            'label' => 'Remember Me',
            'rules' =>  'integer'
        )
    ),
    'register' => array(
        array(
            'field' => 'email',
            'label' => 'Email Address',
            'rules' =>  'trim|required|xss_clean|valid_email'
        ),
        array(
            'field' => 'first_name',
            'label' => 'First Name',
            'rules' =>  'trim|required|xss_clean'
        ),
        array(
            'field' => 'last_name',
            'label' => 'Last Name',
            'rules' =>  'trim|required|xss_clean'
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' =>  'trim|required|xss_clean|min_length[4]|max_length[20]|alpha_dash'
        ),
        array(
            'field' => 'confirm_password',
            'label' => 'Confirm Password',
            'rules' =>  'trim|required|xss_clean|matches[password]'
        ),
    )
);