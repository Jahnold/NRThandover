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
class User extends DataMapper {

    //var $name = "";
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	var $model = 'user';
	var $table = 'users';

	// You can override the database connections with this option
	// var $db_params = 'db_config_name';

	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------

	// Insert related models that Region can have just one of.
	var $has_one = array('');

	// Insert related models that Region can have more than one of.
	var $has_many = array('issue','comment');


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
        //$this->name = $this->first_name . " " . $this->last_name;
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

    function get_initials() {

        $f_initial = substr($this->first_name,1);
        $l_initial = substr($this->last_name,1);

        return $f_initial.$l_initial;
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
