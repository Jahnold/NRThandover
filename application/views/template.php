<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Matthew
 * Date: 19/01/14
 * Time: 18:20
 * To change this template use File | Settings | File Templates.
 */

$this->load->view('header');

if ($navbar) {
    $this->load->view($navbar);
}


$this->load->view($main_content);

$this->load->view('footer');
?>