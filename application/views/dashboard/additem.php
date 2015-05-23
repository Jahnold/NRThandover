<?php

$title = array(
    'name'	=> 'title',
    'id'	=> 'title',
    'value' => set_value('title'),
    'maxlength'	=> 255,
    'size'	=> 30,
    'class' => form_error('title') !== '' ? 'form-control error' : 'form-control',   // if there is an error add the error class
    'label' => 'Item Title'
);
$locations = array(
    'name'	=> 'locations',
    'id'	=> 'locations',
    'value' => set_value('locations'),
    'size'	=> 30,
    'class' => form_error('locations') !== '' ? 'form-control error' : 'form-control',   // if there is an error add the error class
    'label' => 'Locations'
);
$staff = array(
    'name'	=> 'staff',
    'id'	=> 'staff',
    'value' => set_value('staff'),
    'size'	=> 30,
    'class' => form_error('staff') !== '' ? 'form-control error' : 'form-control',   // if there is an error add the error class
    'label' => 'Staff'
);
$tags = array(
    'name'	=> 'tags',
    'id'	=> 'tags',
    'value' => set_value('tags'),
    'size'	=> 30,
    'class' => form_error('tags') !== '' ? 'form-control error' : 'form-control',   // if there is an error add the error class
    'label' => 'Tags'
);
$comments = array(
    'name'	=> 'comments',
    'id'	=> 'comments',
    'value' => set_value('comments'),
    'columns'	=> 30,
    'rows'  => 10,
    'class' => form_error('comments') !== '' ? 'form-control error' : 'form-control',   // if there is an error add the error class
    'label' => 'Initial Comments'
);
$status = array(
    'options'   => array(
        'In Progress'   => 'In Progress',
        'Awaiting Response'  => 'Awaiting Response',
        'Completed' => 'Completed'
    ),
    'selected'  => 'progress',
    'label'     => 'Status',
    'id'        => 'status',
    'name'      => 'status',
    'class'     => 'form-control'
);
$button = array(
    'type'  => 'submit',
    'content'   => 'Add Item',
    'class' => 'btn btn-primary',
    'id'    => 'additem-submit',
    'name'  => 'additem-submit'
);
$cancel = array(
    'type'  => 'button',
    'content'   => 'Cancel',
    'class' => 'btn btn-default',
    'id'    => 'additem-cancel',
    'name'  => 'additem-cancel'
);
$form = array(
    'id'    => 'add-item-form'
);
?>
<script>
    var locations = <?php echo $offices_json ?>;
    var staff = <?php echo $staff_json ?>;
    var tags = <?php echo $tags_json ?>;
</script>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<p>&nbsp;</p>
<?php
echo validation_errors();
echo form_open(base_url('dashboard/additem'),$form);

echo tbs_form_input($title);
echo form_error('title', '<p id="title-error">','</p>');

echo tbs_form_input($locations);
echo tbs_form_input($staff);
echo tbs_form_input($tags);
echo tbs_form_textarea($comments);
echo tbs_form_dropdown($status);
echo form_button($button);
echo anchor(site_url('dashboard'),'<span class="btn btn-default" id="additem-cancel">Cancel</span>');

echo form_close();

?>
</div>
</div>