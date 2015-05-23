<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form', 'url'));
        $this->load->library('tank_auth');
        $this->load->library('form_validation');
		$this->load->library('security');
		//->load->library('tank_auth');
		$this->lang->load('tank_auth');
	}

	function index()
	{
		if ($message = $this->session->flashdata('message')) {
			$this->load->view('auth/general_message', array('message' => $message));
		} else {
			redirect('/auth/login/');
		}
	}

	/**
	 * Login user on the site
	 *
	 * @return void
	 */
	function login()
	{
        // check whether we are doing AJAX or NoJS login
        // then run the appropriate login function
        if ($this->input->post('ajax')) {
            $this->_ajax_login();
        }
        else {
            $this->_noJS_login();
        }
	}

    /**
     * If the user is a numpty and doesn't have JavaScript do an old fashion login
     *
     * @return  void
     *
     */
    function _noJS_login() {

        // user already logged in
        if ($this->tank_auth->is_logged_in()) {
            redirect('');
        }
        // logged in but not activated
        elseif ($this->tank_auth->is_logged_in(FALSE)) {
            redirect('/auth/send_again/');
        }
        // not logged in
        else {

            $data['errors'] = array();

            if ($this->form_validation->run('login')) {

                // validation ok, let's try and log in

                $login  = $this->form_validation->set_value('login');
                $pass   = $this->form_validation->set_value('password');
                $remeb  = $this->form_validation->set_value('remember');

                if ($this->tank_auth->login($login,$pass,$remeb)) {

                    // login successful
                    redirect('');
                }
                else {

                    // login failed

                    $errors = $this->tank_auth->get_error_message();
                    if (isset($errors['banned'])) {

                        // banned user
                        $this->_show_message($this->lang->line('auth_message_banned').' '.$errors['banned']);

                    } elseif (isset($errors['not_activated'])) {

                        // not activated user
                        redirect('/auth/send_again/');

                    } else {

                        // login incorrect
                        foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
                    }
                }
            }

            // load view
            $data['navbar'] = 'navbar_auth';
            $data['main_content'] = 'auth/nojs/login_form';
            $this->load->view('template', $data);

        }
    }

    /**
     * If we're using AJAX then follow this login process
     *
     * @return  void
     *
     */
    function _ajax_login() {

        // user already logged in
        if ($this->tank_auth->is_logged_in()) {

            $data['json'] = array('result' => '1');
        }
        // logged in but not activated
        elseif ($this->tank_auth->is_logged_in(FALSE)) {

            $data['json'] = array('result' => '0', 'reason' => 'not_activated','active' => '0');
        }
        // not logged in
        else {

            // validate form input
            if (!$this->form_validation->run('login')) {

                // form validation failed
                $data['json'] = array('result' => '0', 'reason' => 'validation_failed', 'errors' => $this->form_validation->error_array());

            }
            else {

                // validated, let's try logging in.
                $login  = $this->form_validation->set_value('login');
                $pass   = $this->form_validation->set_value('password');
                $remeb  = $this->form_validation->set_value('remember');

                if (!$this->tank_auth->login($login,$pass,$remeb)) {

                    // login failed
                    $errors = $this->tank_auth->get_error_message();
                    if (isset($errors['banned'])) {

                        // banned user
                        $data['json'] = array('result' => '0', 'reason' => 'banned', 'banned' => '1');

                    } elseif (isset($errors['not_activated'])) {

                        // not activated user
                        $data['json'] = array('result' => '0', 'reason' => 'not_activated', 'active' => '0');

                    } else {

                        // incorrect login
                        $data['json'] = array('result' => '0', 'reason' => 'incorrect_login','errors' => $errors);
                    }
                }
                else {

                    // login success!
                    $data['json'] = array('result' => '1');
                }

            }
        }

        // send the json back to the client
        $this->load->view('json', $data);
    }

	/**
	 * Show info message
	 *
	 * @param	string
	 * @return	void
	 */
	function _show_message($message)
	{
		$this->session->set_flashdata('message', $message);
		redirect('');
	}

	/**
	 * Logout user
	 *
	 * @return void
	 */
	function logout()
	{
		$this->tank_auth->logout();

		$this->_show_message($this->lang->line('auth_message_logged_out'));
	}

	/**
	 * Register user on the site
	 *
	 * @return void
	 */
	function _register() {

        // check whether we are doing AJAX or NoJS register
        // then run the appropriate login function
        if ($this->input->post('ajax')) {
            $this->_ajax_register();
        }
        else {
            $this->_noJS_register();
        }
	}

    /**
     * Register using AJAX
     *
     * @return void
     */
    function _ajax_register() {

        // user already logged in, redirect to dashboard
        if ($this->tank_auth->is_logged_in()) {
            $data['json'] = array('result' => '0', 'reason' => 'logged_in');
        }
        // logged in, not activated
        elseif ($this->tank_auth->is_logged_in(FALSE)) {
            $data['json'] = array('result' => '0', 'reason' => 'not_activated');
        }
        // registration is off
        elseif (!$this->config->item('allow_registration', 'tank_auth')) {
            $data['json'] = array('result' => '0', 'reason' => 'registration_disabled');
        }
        // ok to register
        else {

            $data['errors'] = array();

            $email_activation = $this->config->item('email_activation', 'tank_auth');

            if (!$this->form_validation->run('register')) {

                // validation failed
                $data['json'] = array('result' => '0', 'reason' => 'validation_failed', 'errors' => $this->form_validation->error_array());
            }
            else {

                // validation ok, let's try and register

                $user  = '';
                $email = $this->form_validation->set_value('email');
                $pass  = $this->form_validation->set_value('password');
                $first = $this->form_validation->set_value('first_name');
                $last  = $this->form_validation->set_value('last_name');

                if (!is_null($data = $this->tank_auth->create_user($user,$email,$pass,$first,$last,$email_activation))) {

                    // registration success
                    // now either send activation email or send welcome email
                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    if ($email_activation) {
                        // send "activate" email
                        $data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

                        $this->_send_email('activate', $data['email'], $data);
                        $email_sent = "activation";
                    }
                    else {
                        if ($this->config->item('email_account_details', 'tank_auth')) {

                            // send "welcome" email
                            $this->_send_email('welcome', $data['email'], $data);
                            $email_sent = 'welcome';
                        }
                        else {
                            // no email
                            $email_sent = 'none';
                        }
                    }

                    // Clear password (just in case)
                    unset($data['password']);

                    // set json output
                    $data['json'] = array('result' => '1', 'email' => $email_sent);
                }
                else {

                    // registration failed
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
                    $data['json'] = array('result' => '0', 'reason' => 'registration_failed', 'errors' => $errors);
                }
            }

            // send the json back to the client
            $this->load->view('json', $data);

        }

    }

    /**
     * Register without Javascript
     *
     * @return void
     */
    function _noJS_register() {

        // user already logged in, redirect to dashboard
        if ($this->tank_auth->is_logged_in()) {
            redirect('/dashboard/');
        }
        // logged in, not activated
        elseif ($this->tank_auth->is_logged_in(FALSE)) {
            redirect('/auth/send_again/');
        }
        // registration is off
        elseif (!$this->config->item('allow_registration', 'tank_auth')) {
            $this->_show_message($this->lang->line('auth_message_registration_disabled'));
        }
        // ok to register
        else {

            $data['errors'] = array();

            $email_activation = $this->config->item('email_activation', 'tank_auth');

            if ($this->form_validation->run('register')) {								// validation ok
                if (!is_null($data = $this->tank_auth->create_user(
                    '',
                    $this->form_validation->set_value('email'),
                    $this->form_validation->set_value('password'),
                    $this->form_validation->set_value('first_name'),
                    $this->form_validation->set_value('last_name'),
                    $email_activation))) {									// success

                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    if ($email_activation) {									// send "activate" email
                        $data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

                        $this->_send_email('activate', $data['email'], $data);

                        unset($data['password']); // Clear password (just for any case)

                        $this->_show_message($this->lang->line('auth_message_registration_completed_1'));

                    } else {
                        if ($this->config->item('email_account_details', 'tank_auth')) {	// send "welcome" email

                            $this->_send_email('welcome', $data['email'], $data);
                        }
                        unset($data['password']); // Clear password (just for any case)

                        $this->_show_message($this->lang->line('auth_message_registration_completed_2').' '.anchor('/auth/login/', 'Login'));
                    }
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
                }
            }

            $data['navbar'] = 'navbar_auth';
            $data['main_content'] = 'auth/nojs/register_form';
            $this->load->view('template', $data);

        }
    }


	/**
	 * Send email message of given type (activate, forgot_password, etc.)
	 *
	 * @param	string
	 * @param	string
	 * @param	array
	 * @return	void
	 */
	function _send_email($type, $email, &$data)
	{
		$this->load->library('email');
		$this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
		$this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
		$this->email->to($email);
		$this->email->subject(sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('website_name', 'tank_auth')));
		$this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
		$this->email->set_alt_message($this->load->view('email/'.$type.'-txt', $data, TRUE));
		$this->email->send();
	}

	/**
	 * Send activation email again, to the same or new email address
	 *
	 * @return void
	 */
	function send_again()
	{
		if (!$this->tank_auth->is_logged_in(FALSE)) {							// not logged in or activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->change_email(
						$this->form_validation->set_value('email')))) {			// success

					$data['site_name']	= $this->config->item('website_name', 'tank_auth');
					$data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

					$this->_send_email('activate', $data['email'], $data);

					$this->_show_message(sprintf($this->lang->line('auth_message_activation_email_sent'), $data['email']));

				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/send_again_form', $data);
		}
	}

	/**
	 * Activate user account.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function activate()
	{
		$user_id		= $this->uri->segment(3);
		$new_email_key	= $this->uri->segment(4);

		// Activate user
		if ($this->tank_auth->activate_user($user_id, $new_email_key)) {		// success
			$this->tank_auth->logout();
			$this->_show_message($this->lang->line('auth_message_activation_completed').' '.anchor('/auth/login/', 'Login'));

		} else {																// fail
			$this->_show_message($this->lang->line('auth_message_activation_failed'));
		}
	}

	/**
	 * Generate reset code (to change password) and send it to user
	 *
	 * @return void
	 */
	function forgot_password()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('');

		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');

		} else {
			$this->form_validation->set_rules('login', 'Email or login', 'trim|required|xss_clean');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->forgot_password(
						$this->form_validation->set_value('login')))) {

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					// Send email with password activation link
					$this->_send_email('forgot_password', $data['email'], $data);

					$this->_show_message($this->lang->line('auth_message_new_password_sent'));

				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/forgot_password_form', $data);
		}
	}

	/**
	 * Replace user password (forgotten) with a new one (set by user).
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function reset_password()
	{
		$user_id		= $this->uri->segment(3);
		$new_pass_key	= $this->uri->segment(4);

		$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash');
		$this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

		$data['errors'] = array();

		if ($this->form_validation->run()) {								// validation ok
			if (!is_null($data = $this->tank_auth->reset_password(
					$user_id, $new_pass_key,
					$this->form_validation->set_value('new_password')))) {	// success

				$data['site_name'] = $this->config->item('website_name', 'tank_auth');

				// Send email with new password
				$this->_send_email('reset_password', $data['email'], $data);

				$this->_show_message($this->lang->line('auth_message_new_password_activated').' '.anchor('/auth/login/', 'Login'));

			} else {														// fail
				$this->_show_message($this->lang->line('auth_message_new_password_failed'));
			}
		} else {
			// Try to activate user by password key (if not activated yet)
			if ($this->config->item('email_activation', 'tank_auth')) {
				$this->tank_auth->activate_user($user_id, $new_pass_key, FALSE);
			}

			if (!$this->tank_auth->can_reset_password($user_id, $new_pass_key)) {
				$this->_show_message($this->lang->line('auth_message_new_password_failed'));
			}
		}
		$this->load->view('auth/reset_password_form', $data);
	}

	/**
	 * Change user password
	 *
	 * @return void
	 */
	function change_password()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash');
			$this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->change_password(
						$this->form_validation->set_value('old_password'),
						$this->form_validation->set_value('new_password'))) {	// success
					$this->_show_message($this->lang->line('auth_message_password_changed'));

				} else {														// fail
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/change_password_form', $data);
		}
	}

	/**
	 * Change user email
	 *
	 * @return void
	 */
	function change_email()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->set_new_email(
						$this->form_validation->set_value('email'),
						$this->form_validation->set_value('password')))) {			// success

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					// Send email with new email address and its activation link
					$this->_send_email('change_email', $data['new_email'], $data);

					$this->_show_message(sprintf($this->lang->line('auth_message_new_email_sent'), $data['new_email']));

				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/change_email_form', $data);
		}
	}

	/**
	 * Replace user email with a new one.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function reset_email()
	{
		$user_id		= $this->uri->segment(3);
		$new_email_key	= $this->uri->segment(4);

		// Reset email
		if ($this->tank_auth->activate_new_email($user_id, $new_email_key)) {	// success
			$this->tank_auth->logout();
			$this->_show_message($this->lang->line('auth_message_new_email_activated').' '.anchor('/auth/login/', 'Login'));

		} else {																// fail
			$this->_show_message($this->lang->line('auth_message_new_email_failed'));
		}
	}

	/**
	 * Delete user from the site (only when user is logged in)
	 *
	 * @return void
	 */
	function unregister()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->delete_user(
						$this->form_validation->set_value('password'))) {		// success
					$this->_show_message($this->lang->line('auth_message_unregistered'));

				} else {														// fail
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/unregister_form', $data);
		}
	}

}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */