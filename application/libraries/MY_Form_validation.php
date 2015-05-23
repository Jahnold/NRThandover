<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Matthew
 * Date: 12/02/14
 * Time: 01:41
 * To change this template use File | Settings | File Templates.
 */
class MY_Form_validation extends CI_Form_validation {

    public function error_array() {
        return $this->_error_array;
    }

}