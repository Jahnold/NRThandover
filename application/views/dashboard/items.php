<?php if (!$issues->exists()) : ?>
    <p>No records found</p>
<?php else : ?>
    <?php foreach($issues as $issue) : ?>
        <?php

        // work out whether the issue has been 'touched' since last view
        $highlight = (DateTime::createFromFormat('Y-m-d H:i:s', $issue->touched) > DateTime::createFromFormat('Y-m-d H:i:s', $last_view)) ? ' highlight' : '';


        // if the issue is more than 5 days old make the date red
        $issue_date = DateTime::createFromFormat('Y-m-d H:i:s', $issue->created);
        $worry_date = new DateTime('5 days ago');
        $date_class =  ($issue_date <= $worry_date AND $issue->status !== 'Completed') ? 'danger' : 'standard';

        // set the class of the status button
        if ($issue->status == 'In Progress') {
            $status_class = 'info';
        }
        if ($issue->status == 'Awaiting Response') {
            $status_class = 'warning';
        }
        if ($issue->status == 'Completed') {
            $status_class = 'success';
        }

        $status_button = array(
            'id'        =>  'btn-status-' . $issue->id,
            'class'     =>  'btn-xs btn-' . $status_class,
            'label'     =>  $issue->status,
            'ul_class'  =>  'text-left',
            'menu_items'=>  array(
                'In Progress'   => array(
                    'href'          => '#',
                    'class'         => 'btn-issue-status',
                    'data-status'   => 'progress',
                    'data-id'       => $issue->id
                ),
                'Awaiting Response'   => array(
                    'href'          => '#',
                    'class'         => 'btn-issue-status',
                    'data-status'   => 'awaiting',
                    'data-id'       => $issue->id
                ),
                'Completed'   => array(
                    'href'          => '#',
                    'class'         => 'btn-issue-status',
                    'data-status'   => 'completed',
                    'data-id'       => $issue->id
                )
            )
        );

        $issue_user = new Worker($issue->user_id);

        ?>
        <div class="panel panel-default" id="issue-<?php echo $issue->id; ?>" data-title="<?php echo $issue->title; ?>">
            <div class="panel-heading<?php echo $highlight; ?>">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="panel-title pull-left">
                            <a id="title-text-<?php echo $issue->id; ?>" data-toggle="collapse" data-parent="#issue-<?php echo $issue->id; ?>" href="#issue-<?php echo $issue->id; ?>-collapse" class="panel-toggle collapsed"><?php echo $issue->title; ?></a>
                        </h4>
                    </div>
                    <div class="col-md-2">
                        <span class="label label-<?php echo $date_class; ?>"><?php echo $issue->created; ?></span>
                        <span class="label label-standard label-tooltip" data-tooltip="<?php echo $issue_user->first_name . " " . $issue_user->last_name;?>"><?php echo $issue_user->initials;?></span>
                    </div>
                    <div class="col-md-2 text-right">
                        <?php echo tbs_ddbutton($status_button); ?>
                    </div>
                </div>
            </div>
            <?php   // ************  locations, staff & tags ************
                    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            ?>
            <ul class="list-group">
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-4">
                            <button class="btn btn-default btn-xs btn-edit-associations" data-issue="<?php echo $issue->id; ?>" data-type="locations"><span class="glyphicon glyphicon-home opacity35"></span></button>
                            <div id="locations-<?php echo $issue->id; ?>" class="div-inline">
                                <?php foreach ($issue->office->get() as $location) : ?>
                                    <span class="label label-<?php echo $location->region_id; ?> association-locations" data-id="<?php echo $location->id; ?>" data-region="<?php echo $location->region_id; ?>"><?php echo $location->name; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-default btn-xs btn-edit-associations" data-issue="<?php echo $issue->id; ?>" data-type="staff"><span class="glyphicon glyphicon-user opacity35"></span></button>
                            <div id="staff-<?php echo $issue->id; ?>" class="div-inline">
                                <?php foreach ($issue->staff->get() as $staff) : ?>
                                    <span class="label label-standard association-staff" data-id="<?php echo $staff->id; ?>"><?php echo $staff->first_name . " " . $staff->last_name; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-default btn-xs btn-edit-associations" data-issue="<?php echo $issue->id; ?>" data-type="tags"><span class="glyphicon glyphicon-tags opacity35"></span></button>
                            <div id="tags-<?php echo $issue->id; ?>" class="div-inline">
                                <?php foreach ($issue->tag->get() as $tag) : ?>
                                    <span class="label label-standard association-tags" data-id="<?php echo $tag->id; ?>"><?php echo $tag->name; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <?php   // ************  comments ************
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            ?>
            <div id="issue-<?php echo $issue->id; ?>-collapse" class="panel-collapse collapse issue-collapse">
                <ul class="list-group" id="comment-list-<?php echo $issue->id; ?>">

                    <?php $issue->comment->get();
                    foreach ($issue->comment as $comment) :
                        $user = new Worker($comment->user_id);?>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-10">
                                    <p id="comment-text-<?php echo $comment->id; ?>"><?php echo $comment->text; ?></p>
                                </div>
                                <div class="col-md-2 text-right">
                                    <?php if ($comment->user_id == $user_id) : ?>
                                    <button class="btn btn-default btn-xs btn-edit-comment" data-id="<?php echo $comment->id; ?>"><span class="glyphicon glyphicon-pencil opacity35"></span></button>
                                    <?php endif; ?>
                                    <span class="label label-standard label-tooltip" data-tooltip="<?php echo $user->first_name . " " . $user->last_name;?>"><?php echo $user->initials;?></span>
                                    <span class="label label-standard label-tooltip" data-tooltip="<?php echo $comment->created; ?>"><span class="glyphicon glyphicon-time"></span></span>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <li class="list-group-item hidden" id="new-comment-li-<?php echo $issue->id;?>">
                        <div class="form-group">
                            <label for="new-comment-<?php echo $issue->id;?>">New Comment:</label>
                            <textarea id="new-comment-<?php echo $issue->id;?>" class="form-control"></textarea>
                        </div>
                        <button class="btn btn-primary btn-sm btn-save-new-comment" data-issueid="<?php echo $issue->id;?>">Save</button>
                        <button class="btn btn-default btn-sm btn-cancel-new-comment" data-issueid="<?php echo $issue->id;?>">Cancel</button>
                    </li>
                </ul>
                <div class="panel-footer">
                    <button class="btn btn-default btn-sm btn-add-new-comment" data-issueid="<?php echo $issue->id;?>">Add Comment</button>
                    <button class="btn btn-default btn-sm btn-edit-title" data-issueid="<?php echo $issue->id;?>">Edit Title</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>