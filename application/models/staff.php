<?php

/**
 * Region DataMapper Model
 *
 * Use this basic model as a region for creating new models.
 * It is not recommended that you include this file with your application,
 * especially if you use a Region library (as the classes may collide).
 *
 * To use:
 * 1) Copy this file to the lowercase name of your new model.
 * 2) Find-and-replace (case-sensitive) 'Region' with 'Your_model'
 * 3) Find-and-replace (case-sensitive) 'region' with 'your_model'
 * 4) Find-and-replace (case-sensitive) 'regions' with 'your_models'
 * 5) Edit the file as desired.
 *
 * @license		MIT License
 * @category	Models
 * @author		Phil DeJarnett
 * @link		http://www.overzealous.com
 */
class Staff extends DataMapper {

    var $name = "";
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	var $model = 'staff';
	var $table = 'staff';

	// You can override the database connections with this option
	// var $db_params = 'db_config_name';

	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------

	// Insert related models that Region can have just one of.
	var $has_one = array('office');

	// Insert related models that Region can have more than one of.
	var $has_many = array('issue');


	// --------------------------------------------------------------------
	// Validation
	//   Add validation requirements, such as 'required', for your fields.
	// --------------------------------------------------------------------

/*	var $validation = array(
		'example' => array(
			// example is required, and cannot be more than 120 characters long.
			'rules' => array('required', 'max_length' => 120),
			'label' => 'Example'
		)
	);*/

	// --------------------------------------------------------------------
	// Default Ordering
	//   Uncomment this to always sort by 'name', then by
	//   id descending (unless overridden)
	// --------------------------------------------------------------------

	// var $default_order_by = array('name', 'id' => 'desc');

	// --------------------------------------------------------------------

	/**
	 * Constructor: calls parent constructor
	 */
    function __construct($id = NULL)
	{
		parent::__construct($id);

        // set up the full name
        $this->name = $this->first_name . " " . $this->last_name;
    }

	// --------------------------------------------------------------------
	// Post Model Initialisation
	//   Add your own custom initialisation code to the Model
	// The parameter indicates if the current config was loaded from cache or not
	// --------------------------------------------------------------------
	function post_model_init($from_cache = FALSE)
	{
	}

	// --------------------------------------------------------------------
	// Custom Methods
	//   Add your own custom methods here to enhance the model.
	// --------------------------------------------------------------------

    function get_json() {

        $objects = $this->get();
        $temp_array = array();

        foreach ($objects as $ob) {
            $temp_array[] = array('value' => $ob->id, 'text' => $ob->first_name . " " . $ob->last_name);
        }

        return json_encode($temp_array);

    }

    /**
     * Produces an array of all Issues which are associated with the Offices in $location
     * $and is a bool which specifies whether we are doing an AND search or and OR search
     *
     * @property array
     * @property bool
     *
     * @return array
     *
     * */
    function get_issue_ids($staff, $and=FALSE) {

        if (count($staff) == 0) {

            // no locations who knows what we're doing here
            return FALSE;
        }
        elseif (count($staff) == 1) {

            // only one location simple where
            $q = "SELECT issue_id FROM issues_staff WHERE staff_id = ?";
            $results = $this->db->query($q,$staff[0]);
        }
        else {

            // multiple locations are we doing OR or AND
            if ($and) {

                // build AND query
                $q =  'SELECT t1.issue_id ';
                $q .= 'FROM issues_staff t1 ';

                $count = 0;
                while ($count < count($staff) - 1) {

                    $t1 = $count + 1;
                    $t2 = $count + 2;

                    $q .= "JOIN issues_staff t{$t2} ON t{$t1}.issue_id = t{$t2}.issue_id AND t{$t2}.staff_id = '{$staff[$count]}' ";

                    $count++;
                }
                $q .= "WHERE t1.staff_id = '{$staff[$count]}'";

            }
            else {

                // build OR query
                $staff_ids = implode(",", $staff);

                $q =  'SELECT issue_id ';
                $q .= 'FROM issues_staff ';
                $q .= "WHERE staff_id IN ({$staff_ids}) ";

            }

            $results = $this->db->query($q);
        }

        // return an array of the results

        if ($results->num_rows() > 0)  {

            foreach ($results->result() as $row)  {
                $issue_ids[] = $row->issue_id;
            }

            return $issue_ids;
        }
        else {
            return FALSE;
        }

    }

	// --------------------------------------------------------------------
	// Custom Validation Rules
	//   Add custom validation rules for this model here.
	// --------------------------------------------------------------------

	/* Example Rule
	function _convert_written_numbers($field, $parameter)
	{
	 	$nums = array('one' => 1, 'two' => 2, 'three' => 3);
	 	if(in_array($this->{$field}, $nums))
		{
			$this->{$field} = $nums[$this->{$field}];
	 	}
	}
	*/
}

/* End of file region.php */
/* Location: ./application/models/region.php */
