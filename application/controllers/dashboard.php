<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Matthew
 * Date: 28/01/14
 * Time: 19:22
 * To change this template use File | Settings | File Templates.
 */
class Dashboard extends CI_Controller {

    public function __construct() {

        parent::__construct();

        // load helpers/libraries
        $this->load->library('form_validation');

        // if not logged in send back to welcome screen
        if (!$this->tank_auth->is_logged_in()) {
            redirect('');
        }
    }

    public function index() {

        // load all the currently open issues by default (may change this later)
        $issues = new Issue();
        $issues->where('status !=', 'Completed');
        $issues->get_iterated();

        // get the current user_id
        $data['user_id'] = $this->tank_auth->get_user_id();

        // load the last_view date from the user settings and then update it
        $settings = new Setting();
        $settings->get_by_user_id($this->tank_auth->get_user_id());
        $data['last_view'] = $settings->last_view;
        $settings->last_view = date("Y-m-d H:i:s");
        $settings->save();

        // json string of all offices
        $offices = new Office();
        $data['offices_json'] = $offices->get_json();
        // json string of all staff
        $staff = new Staff();
        $data['staff_json'] = $staff->get_json();
        // json string for all tags
        $tags = new Tag();
        $data['tags_json'] = $tags->get_json();

        $data['issues'] = $issues;
        $data['main_content'] = "dashboard/dashboard";
        $data['navbar'] = "navbar_internal";
        $this->load->view('template', $data);
    }

    /*
     * Start a new issue
     */
    public function additem() {

        // set all the data needed

        // json string of all offices
        $offices = new Office();
        $data['offices_json'] = $offices->get_json();
        // json string of all staff
        $staff = new Staff();
        $data['staff_json'] = $staff->get_json();
        // json string for all tags
        $tags = new Tag();
        $data['tags_json'] = $tags->get_json();

        // view options
        $data['main_content'] = "dashboard/additem";
        $data['navbar'] = 'navbar_internal';

        // check whether we are posting back to ourselves
        if ($this->input->post('title')) {
            // form has been filled check and insert into DB
            // start with some validation
            $this->form_validation->set_rules('title', 'Title', 'required|trim');
            $this->form_validation->set_rules('comments', 'Initial Comments', 'required|trim');
            if ($this->form_validation->run() == FALSE) {
                // validation failed...reload the form
                $this->load->view('template', $data);
            }
            else {
                // validation passed

                // first create the issue
                $issue = new Issue();
                $issue->user_id = $this->tank_auth->get_user_id();
                $issue->title = $this->input->post('title');
                $issue->status = $this->input->post('status');
                $issue->touched = date("Y-m-d H:i:s");
                $issue->save();

                // then create the comment and associate it with the new issue
                $comment = new Comment();
                $comment->user_id = $this->tank_auth->get_user_id();
                $comment->text = $this->input->post('comments');
                $comment->save($issue);

                // load and save staff associated with this issue
                // first check whether we have any staff
                if (strlen($this->input->post('staff')) > 0) {
                    $staff = new Staff;
                    $ids = explode(",", $this->input->post('staff'));
                    foreach ($ids as $id) {
                        $staff->or_where('id', $id);
                    }
                    $staff->get();
//                    var_dump($staff);
                    $issue->save($staff->all);
                }

                // load and save any locations associated with this issue
                if (strlen($this->input->post('locations')) > 0) {
                    $locations = new Office();
                    $ids = explode(",", $this->input->post('locations'));
                    foreach ($ids as $id) {
                        $locations->or_where('id', $id);
                    }
                    $locations->get();
                    $issue->save($locations->all);
                }

                // load and save any tags associated with this issue
                if (strlen($this->input->post('tags')) > 0) {
                    $tags = new Tag();
                    $ids = explode(",", $this->input->post('tags'));
                    foreach ($ids as $id) {
                        $tags->or_where('id', $id);
                    }
                    $tags->get();
                    $issue->save($tags->all);
                }

                // load the dashboard
                redirect('dashboard');
            }
        }
        else {

            // form hasn't been filled yet load the view
            $data['userid'] = '';


            $this->load->view('template', $data);
        }
    }

    public function tags_json() {

        $tags = new Tag();
        $data['json'] = $tags->get_json();

        $this->load->view('json', $data);
    }

    /**
     * Function which adds a comment to an issue
     * Data arrives by post via ajax
     * Responds with JSON
     *
     * @return string
     */
    public function save_comment() {

        if ($this->input->post('id')) {
            // we've received some post data
            $this->form_validation->set_rules('id', 'id', 'required|integer');
            $this->form_validation->set_rules('text', 'text', 'required|trim');

            if ($this->form_validation->run()) {
                // validates, let's stick it in the db
                $issue = new Issue();

                if (!$issue->get_where(array('id' => $this->input->post('id')))) {
                    // something went wrong
                    $data['json'] = array('result' => '0', 'message' => 'issue not found');
                }
                else {
                    // good, one issue
                    $comment = new Comment();
                    $comment->user_id = $this->tank_auth->get_user_id();
                    $comment->text = $this->input->post('text');

                    if($comment->save($issue)) {
                        // saved all good

                        // just update the touched field on the issue
                        $issue->touched = date("Y-m-d H:i:s");
                        $issue->save();

                        $user = new Worker($comment->user_id);
                        $data['json'] = array('result' => '1', 'message' => 'sweet deal', 'username' => $user->first_name . " ".$user->last_name, 'userinitials' => $user->initials, 'commentid' => $comment->id, 'created' => $comment->created, 'issueid' => $issue->id);
                    }
                    else {
                        // something went wrong with the save
                        $data['json'] = array('result' => '0', 'message' => 'db save failed');
                    }
                }
            }
            else {
                // validation failed
                $data['json'] = array('result' => '0', 'message' => 'validation failed');
            }
        }
        else {
            // incorrect or no post data
            $data['json'] = array('result' => '0', 'message' => 'incorrect post data');
        }

        $this->load->view('json', $data);

    }

    /**
     * Function which updates the status of an issue
     * Called via AJAX
     * Returns JSON
     *
     * @return string
     *
     */
    public function update_status() {

        $result = 1;
        $message = "sweet deal";

        // did we receive data
        if (!$this->input->post('id')) {

            $result = 0;
            $message = "incorrect post data";
        }

        // is the data valid
        $statuses = array('completed' => 'Completed', 'awaiting' => 'Awaiting Response', 'progress' => 'In Progress');
        if ($result == 1 AND  !array_key_exists($this->input->post('status'),$statuses)) {

            $result = 0;
            $message = "unrecognised status";
        }

        // can we load a model
        $issue = new Issue();
        if ($result == 1 AND !$issue->get_where(array('id' => $this->input->post('id')))) {

            $result = 0;
            $message = "issue not found";
        }

        // update the model
        if ($result == 1) {

            $issue->status = $statuses[$this->input->post('status')];

            if ($this->input->post('status') == 'Completed') {
                $issue->closed = date ("Y-m-d H:i:s"); ;
            }
        }

        // does it save
        if ($result == 1 AND !$issue->save()) {

            $result = 0;
            $message = "db save problem";
        }

        // set the json and load the view
        $data['json'] = array('result' => $result, 'message' => $message);
        $this->load->view('json', $data);

    }

    /**
     * Function which updates the associations between and issue and
     * either its locations, staff or tags (abstract ftw)
     * Called via AJAX
     * Returns JSON
     *
     *  @return string
     */
    function update_associations() {

        $result = 1;
        $message = "sweet deal";

        // did we receive data
        if (!$this->input->post('id')) {

            $result = 0;
            $message = "incorrect post data";
        }

        // is the data valid
        $this->form_validation->set_rules('id', 'id', 'required|integer');
        $this->form_validation->set_rules('type', 'text', 'required');
        $this->form_validation->set_rules('items', 'items', 'required');
        if ($result == 1 AND !$this->form_validation->run()) {

            $result = 0;
            $message = "validation failed";
        }

        // can we load a model
        $issue = new Issue();
        if ($result == 1 AND !$issue->get_where(array('id' => $this->input->post('id')))) {

            $result = 0;
            $message = "issue not found";
        }

        // update the relationships
        if ($result == 1) {

            // determine which class we're working with
            $class_type = $this->input->post('type');

            // delete current relationships
            $current = $issue->$class_type->get();
            $issue->delete($current->all);

            $items = new $class_type();
            $ids = explode(",", $this->input->post('items'));
            foreach ($ids as $id) {
                $items->or_where('id', (int)$id);
            }
            $items->get();
        }

        if ($result == 1 AND !$issue->save($items->all)) {

            $result = 0;
            $message = "db save problem";

        }

        // set the json and load the view
        $data['json'] = array('result' => $result, 'message' => $message);
        $this->load->view('json', $data);

    }

    /**
     * Function which updates the text of a title or comment
     * again it's slightly abstracted
     * Called via AJAX post
     * Returns JSON
     */
    public function save_text() {

        $result = 1;
        $message = "sweet deal";

        // did we receive data
        if (!$this->input->post('id')) {

            $result = 0;
            $message = "incorrect post data";
        }

        // is the data valid
        $this->form_validation->set_rules('id', 'id', 'required|integer');
        $this->form_validation->set_rules('type', 'type', 'required');
        $this->form_validation->set_rules('text', 'text', 'required');
        if ($result == 1 AND !$this->form_validation->run()) {

            $result = 0;
            $message = "validation failed";
        }

        // can we load a model
        if ($this->input->post('type') == 'comment') {
            $item = new Comment();
            $field = 'text';
        }
        elseif ($this->input->post('type') == 'title') {
            $item = new Issue();
            $field = 'title';
        }
        else {

            $result = 0;
            $message = "invalid type";
        }
        if ($result == 1 AND !$item->get_where(array('id' => $this->input->post('id')))) {

            $result = 0;
            $message = "issue not found";
        }

        // update the text
        if ($result == 1) {

            $item->$field = $this->input->post('text');

        }

        if ($result == 1 AND !$item->save()) {

            $result = 0;
            $message = "db save problem";

        }

        // set the json and load the view
        $data['json'] = array('result' => $result, 'message' => $message);
        $this->load->view('json', $data);
    }

    /**
     * Function which returns all the issues which match the given filters
     * Called via AJAX post
     * Returns HTML
     */
    public function load_items() {

        $issues = new Issue();
        $return_none = FALSE;

        // status filters

        // if all statuses are unchecked...return none
        if($this->input->post('status') == 'f,f,f') {
            //echo "1";
            $return_none = TRUE;
        }

        // only filter if all three are not selected
        if($this->input->post('status') !== 't,t,t' AND $return_none == FALSE) {

            $status = explode(",", $this->input->post('status'));

            $issues->group_start();

            ($status[0] == 't') ? $issues->or_where('status', 'In Progress') : null;
            ($status[1] == 't') ? $issues->or_where('status', 'Awaiting Response') : null;
            ($status[2] == 't') ? $issues->or_where('status', 'Completed') : null;

            $issues->group_end();
        }

        // check whether there are any label filters
        if($this->input->post('labelFilters') == "TRUE" AND $return_none == FALSE) {

            $matching_issues = array();

            // location filters
            if($this->input->post('locations') !== "") {

                $location_ids = explode(",", $this->input->post('locations'));
                $and_or_or = ($this->input->post('operLocations') == 'AND');

                $locations = new Office();
                $issue_ids = $locations->get_issue_ids($location_ids, $and_or_or);


                if (is_array($issue_ids)) {
                    // we found some issues which match the criteria
                    foreach($issue_ids as $issue_id) {
                        $matching_issues[] = $issue_id;
                    }
                }
                else {
                    // no issues found which match the criteria
                    //echo "2";
                    $return_none = TRUE;
                }

            }

            // staff filters
            if($this->input->post('staff') !== ""  AND $return_none == FALSE) {

                $ids = explode(",", $this->input->post('staff'));
                $and_or_or = ($this->input->post('operStaff') == 'AND');

                $staff = new Staff();
                $issue_ids = $staff->get_issue_ids($ids, $and_or_or);

                if (is_array($issue_ids)) {
                    // we found some issues which match the criteria
                    if ($this->input->post('locations') !== "") {

                        // we've already got some ids for locations
                        // intersect these arrays to find ids in both
                        $matching_issues = array_intersect($matching_issues,$issue_ids);
                        if (count($matching_issues) == 0) {
                            //echo "3";
                            $return_none = TRUE;
                        }
                    }
                    else {
                        // no locations these are the first bunch of ids
                        foreach($issue_ids as $issue_id) {
                            $matching_issues[] = $issue_id;
                        }
                    }
                }
                else {
                    // no issues found which match the criteria
                    //echo "4";
                    $return_none = TRUE;
                }

            }

            // tag filters
            if($this->input->post('tags') !== "" AND $return_none == FALSE) {

                $ids = explode(",", $this->input->post('tags'));
                $and_or_or = ($this->input->post('operTags') == 'AND');

                $tags = new Tag();
                $issue_ids = $tags->get_issue_ids($ids, $and_or_or);

                if (is_array($issue_ids)) {
                    // we found some issues which match the criteria
                    if ($this->input->post('locations') !== "" OR $this->input->post('staff') !== "") {

                        // we've already got some ids for locations or staff
                        // intersect these arrays to find ids in both
                        $matching_issues = array_intersect($matching_issues,$issue_ids);
                        if (count($matching_issues) == 0) {
                            //echo "5";
                            $return_none = TRUE;
                        }
                    }
                    else {
                        // no locations these are the first bunch of ids
                        foreach($issue_ids as $issue_id) {
                            $matching_issues[] = $issue_id;
                        }
                    }
                }
                else {
                    // no issues found which match the criteria
                    //echo "6";
                    $return_none = TRUE;
                }

            }

            $issues->where_in('id',$matching_issues);

        }// end of label filters

        // date filters
        if ($this->input->post('dateFrom') !== "" AND $this->input->post('dateTo') !== "") {

            $from = DateTime::createFromFormat('d/m/Y H:i:s', $this->input->post('dateFrom') . "00:00:00");
            $to = DateTime::createFromFormat('d/m/Y H:i:s', $this->input->post('dateTo') . "23:59:59");

            $issues->where_between('created',$from->format("Y-m-d H:i:s"), $to->format("Y-m-d H:i:s"));
        }

        // order by
        if ($this->input->post('sortBy') == 'created') {
            $issues->order_by("created", "desc");
        }

        if ($return_none == FALSE) {
            //echo $issues->get_sql();
            $issues->get_iterated();
        }

        // load the last_view date from the user settings and then update it to now
        $settings = new Setting();
        $settings->get_by_user_id($this->tank_auth->get_user_id());
        $last_view = $settings->last_view;
        $settings->last_view = date("Y-m-d H:i:s");
        $settings->save();

        $this->load->view('dashboard/items', array('issues' => $issues, 'last_view' => $last_view, 'user_id' => $this->tank_auth->get_user_id()));

    }
}