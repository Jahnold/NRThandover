<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Matthew
 * Date: 19/03/14
 * Time: 15:02
 * To change this template use File | Settings | File Templates.
 */

class Worker extends CI_Model {

    public $id = "";
    public $first_name = "";
    public $last_name = "";
    public $initials = "";

    function __construct($id)
    {
        parent::__construct();

        $this->get_user_by_id($id);

    }

    /**
     * Get user record by Id
     *
     * @param	int
     * @param	bool
     * @return	object
     */
    function get_user_by_id($user_id)
    {
        $this->db->where('id', $user_id);

        $query = $this->db->get('users');
        if ($query->num_rows() == 1) {
            $row = $query->row();
            $this->id = $row->id;
            $this->first_name = $row->first_name;
            $this->last_name = $row->last_name;
            $this->initials = substr($row->first_name,0,1) . substr($row->last_name,0,1);
            return $query->row();
        }
        return NULL;
    }

    function get_first_name() {
        return $this->first_name;
    }
}