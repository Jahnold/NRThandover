<script>
    var locations = <?php echo $offices_json ?>;
    var staff = <?php echo $staff_json ?>;
    var tags = <?php echo $tags_json ?>;
</script>
<?php
$locations = array(
    'name'	=> 'locations',
    'id'	=> 'locations',
    'size'	=> 30,
    'class' => 'form-control labels-locations',
    'label' => 'Locations'
);
$staff = array(
    'name'	=> 'staff',
    'id'	=> 'staff',
    'size'	=> 30,
    'class' => 'form-control labels-staff',
    'label' => 'Staff'
);
$tags = array(
    'name'	=> 'tags',
    'id'	=> 'tags',
    'size'	=> 30,
    'class' => 'form-control labels-tags',
    'label' => 'Tags'
);
$save = array(
    'type'  => 'button',
    'content'   => 'Save',
    'class' => 'btn btn-primary',
    'id'    => 'btn-save-associations'
);
$cancel = array(
    'type'  => 'button',
    'content'   => 'Cancel',
    'class' => 'btn btn-default',
    'id'    => 'btn-cancel-associations'
);
$modal = array(
    'id'    => 'associations-dialog',
    'title' => 'Edit'
);

// filter form items
$and_or_button = array(
    'id'        => 'btn-andor-locations',
    'class'     => 'btn-default btn-andor',
    'input_group'=> 'TRUE',
    'label'     => 'OR',
    'ul_class'  => 'pull-right',
    'menu_items'=> array(
        'OR'        => array(
            'href'          => '#',
            'class'         => 'btn-andor-choose',
            'data-andor'    => 'OR',
        ),
        'AND'        => array(
            'href'          => '#',
            'class'         => 'btn-andor-choose',
            'data-andor'    => 'AND',
        )
    )
);

$locations_filter = array(
    'name'	=> 'locations-filter',
    'id'	=> 'locations-filter',
    'size'	=> 30,
    'class' => 'form-control labels-locations',
    'label' => 'Locations',
    'button'=> $and_or_button
);

$and_or_button['id'] = 'btn-andor-staff';
$staff_filter = array(
    'name'	=> 'staff-filter',
    'id'	=> 'staff-filter',
    'size'	=> 30,
    'class' => 'form-control labels-staff',
    'label' => 'Staff',
    'button'=> $and_or_button
);
$and_or_button['id'] = 'btn-andor-tags';
$tags_filter = array(
    'name'	=> 'tags-filter',
    'id'	=> 'tags-filter',
    'size'	=> 30,
    'class' => 'form-control labels-tags',
    'label' => 'Tags',
    'button'=> $and_or_button
);

$dates = array(
    'id'        => 'btn-date-ranges',
    'class'     => 'btn-default',
    'label'     => 'Date Range',
    'ul_class'  => 'pull-left',
    'input_group'=> 'TRUE',
    'menu_items'=> array(
        'This Month'    => array(
            'href'          => '#',
            'class'         => 'btn-date-range',
            'data-range'    => 'tm',
        ),
        'Last 7 Days'     => array(
            'href'          => '#',
            'class'         => 'btn-date-range',
            'data-range'    => 'l7d',
        ),
        'Last 30 Days'     => array(
            'href'          => '#',
            'class'         => 'btn-date-range',
            'data-range'    => 'l30d',
        ),
        'All'     => array(
            'href'          => '#',
            'class'         => 'btn-date-range',
            'data-range'    => 'all',
        )
    )
);

// associations modal
$this->load->view('bootstrap/modal_start', $modal);
?>
<div id="locations-div" class="associations-div hidden">
    <?php echo tbs_form_input($locations); ?>
</div>
<div id="staff-div" class="associations-div hidden">
    <?php echo tbs_form_input($staff); ?>
</div>
<div id="tags-div" class="associations-div hidden">
    <?php echo tbs_form_input($tags); ?>
</div>
<div id="association-data" data-issue="" data-type=""></div>
<?php
$this->load->view('bootstrap/modal_middle');

echo form_button($save);
echo form_button($cancel);

$this->load->view('bootstrap/modal_end');

$title = array(
    'name'	=> 'title',
    'id'	=> 'title',
    'size'	=> 30,
    'class' => 'form-control',
    'label' => 'Title'
);
$comment = array(
    'name'	=> 'comment',
    'id'	=> 'comment',
    'columns'	=> 30,
    'rows'  => 10,
    'class' => 'form-control',
    'label' => 'Comment'
);
$modal_tc = array(
    'id'    => 'edit-dialog',
    'title' => 'Edit'
);
$save_tc = array(
    'type'  => 'button',
    'content'   => 'Save',
    'class' => 'btn btn-primary',
    'id'    => 'btn-save-edit',
    'data-issue' => ''
);
$cancel_tc = array(
    'type'  => 'button',
    'content'   => 'Cancel',
    'class' => 'btn btn-default',
    'id'    => 'btn-cancel-edit',
    'data-dismiss' => 'modal'
);
// title/comments modal
$this->load->view('bootstrap/modal_start', $modal_tc);

?>
<div class="edit-div hidden" id="title-div">
    <?php echo tbs_form_input($title); ?>
</div>
<div class="edit-div hidden" id="comment-div">
    <?php echo tbs_form_textarea($comment); ?>
</div>
<?php
$this->load->view('bootstrap/modal_middle');

echo form_button($save_tc);
echo form_button($cancel_tc);

$this->load->view('bootstrap/modal_end');
?>


<h1>Welcome to NRT Handover</h1>
<div class="panel-group" id="filters">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title pull-left">
                <a data-toggle="collapse" data-parent="#filters" href="#filters-collapse" class="panel-toggle collapsed">Filters</a>
            </h4>
            <button class="btn btn-default btn-xs pull-right">Reset Filters</button>
            <div class="clearfix"></div>
        </div>
        <div id="filters-collapse" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="filter-group">
                            <div><label>Status</label></div>
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default active" id="filter-progress">
                                    <input type="checkbox" name="progress"> In Progress
                                </label>
                                <label class="btn btn-default active" id="filter-awaiting">
                                    <input type="checkbox" name="awaiting"> Awaiting Response
                                </label>
                                <label class="btn btn-default" id="filter-completed">
                                    <input type="checkbox" name="completed"> Completed
                                </label>
                            </div>
                        </div>
                        <div class="filter-group">
                            <div><label>Date Range</label></div>
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control" name="start" id="filter-start-date" />
                                <span class="input-group-addon">to</span>
                                <input type="text" class="input-sm form-control" name="end" id="filter-end-date" />
                                <?php echo tbs_ddbutton($dates); ?>
                            </div>
                        </div>
                        <div class="filter-group">
                            <div><label>Sort By</label></div>
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default active" id="filter-touched">
                                    <input type="radio" name="filter-sort" value="touched"> Updated
                                </label>
                                <label class="btn btn-default" id="filter-created">
                                    <input type="radio" name="filter-sort" value="created"> Created
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="filter-group"><?php echo tbs_input_ddbutton($locations_filter); ?></div>
                        <div class="filter-group"><?php echo tbs_input_ddbutton($staff_filter); ?></div>
                        <div class="filter-group"><?php echo tbs_input_ddbutton($tags_filter); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<p>&nbsp;</p>
<div id="control-buttons">
     <a href="<?php echo site_url('dashboard/additem')?>"><span class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span>Add Item</span></a>
    <button class="btn btn-default" id="btn-refresh" data-loading-text="Loading..."><span class="glyphicon glyphicon-refresh"></span> Refresh</button>
<!--    <button class="btn btn-default pull-right"><span class="glyphicon glyphicon-resize-full"></span></button>-->
<!--    <button class="btn btn-default pull-right"><span class="glyphicon glyphicon-resize-small"></span></button>-->
</div>
<p>&nbsp;</p>
<div id="items">
    <?php $data['issues'] = $issues;
    $data['last_view'] = $last_view;
    $data['user_id'] = $user_id;
    $this->load->view('dashboard/items', $data); ?>
</div>