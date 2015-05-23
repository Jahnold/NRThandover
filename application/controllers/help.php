<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Matthew
 * Date: 27/03/14
 * Time: 11:38
 * To change this template use File | Settings | File Templates.
 */

class Help extends CI_Controller {

    public function __construct() {

        parent::__construct();

        // load helpers/libraries
        $this->load->library('form_validation');

        // if not logged in send back to welcome screen
        if (!$this->tank_auth->is_logged_in()) {
            redirect('');
        }
    }

    function index() {

    }
}