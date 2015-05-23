$( document ).ready(function() {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~
    // set up typeahead and tags
    // ~~~~~~~~~~~~~~~~~~~~~~~~~

    function initTypeAhead(inputName, sourceName) {
        // check to see whether jQuery found anything
        if (inputName.length === 0) {
            // do nothing, it doesn't exist
        }
        else {
            inputName.tagsinput('input').typeahead(null,{
                name: 'tags',
                displayKey: 'text',
                source: sourceName.ttAdapter()
            }).bind('typeahead:selected typeahead:autocompleted', $.proxy(function (obj, datum) {
                    this.tagsinput('add', datum);
                    this.tagsinput('input').typeahead('val', '');
                    this.tagsinput('input').typeahead('close');
                }, inputName));

        }
    }

    function initBloodhound(varName) {

        return new Bloodhound({
            datumTokenizer: function(data) { return Bloodhound.tokenizers.whitespace(data.text); },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            limit: 10,
            local: varName
        });

    }

    // tags

    // set up the bloodhound matching sources
    var tags_source = initBloodhound(tags);
    var locations_source = initBloodhound(locations);
    var staff_source = initBloodhound(staff);

    tags_source.initialize();
    locations_source.initialize();
    staff_source.initialize();

    // set up vars that link to the input fields
    var $tags = $('#tags'),
        $tagsFilter = $('#tags-filter'),
        $locations = $('#locations'),
        $locationsFilter = $('#locations-filter'),
        $staff = $('#staff'),
        $staffFilter = $('#staff-filter');

    // call tagsInput and set options
    $tags.tagsinput({
        itemValue: 'value',
        itemText: 'text'
    });
    $tagsFilter.tagsinput({
        itemValue: 'value',
        itemText: 'text'
    });
    $locations.tagsinput({
        itemValue: 'value',
        itemText: 'text',
        tagClass: function(item) {
            return 'label label-' + item.region;
        }
    });
    $locationsFilter.tagsinput({
        itemValue: 'value',
        itemText: 'text',
        tagClass: function(item) {
            return 'label label-' + item.region;
        }
    });
    $staff.tagsinput({
        itemValue: 'value',
        itemText: 'text'
    });
    $staffFilter.tagsinput({
        itemValue: 'value',
        itemText: 'text'
    });

    // initialise the type ahead
    initTypeAhead($tags,tags_source);
    initTypeAhead($tagsFilter,tags_source);
    initTypeAhead($locations,locations_source);
    initTypeAhead($locationsFilter,locations_source);
    initTypeAhead($staff,staff_source);
    initTypeAhead($staffFilter,staff_source);

    // ~~~~~~~~~~~
    // date picker
    // ~~~~~~~~~~~

    $('#filter-start-date').datepicker({
        format: "dd/mm/yyyy",
        startDate: "01/01/2014",
        endDate: '0',
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function(e) {

        // set the startDate of the end date field
        $('#filter-end-date').datepicker('setStartDate',e.date);
    });

    $('#filter-end-date').datepicker({
        format: "dd/mm/yyyy",
        endDate: "0",
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function(e) {

        // set the endDate of the start date field
        $('#filter-start-date').datepicker('setEndDate',e.date);
    });


    // ~~~~~~~
    // modules
    // ~~~~~~~

    var issuePanel = {

        addComment: function() {

            // get the issue id
            var issueID = $(this).data('issueid');
            // slide down the li for this issue
            $('#new-comment-li-' + issueID).removeClass('hidden');
        },
        saveComment: function() {

            // get the issue id
            var issueID = $(this).data('issueid');
            // get the new comment text
            var $newCommentBox = $('#new-comment-' + issueID);
            var commentText = $newCommentBox.val();

            // check to make sure that there is some text
            if (commentText !== "") {
                // we have text
                // ajax this to the server
                $.ajax({
                    type    : 'POST',
                    url     : 'dashboard/save_comment',
                    dataType: 'json',
                    data    : {
                        'id'    : issueID,
                        'text'  : commentText
                    },
                    success : function(result) {

                        if (result.result == 1) {
                            // comment added successfully
                            // add the comment li to the issue
                            var commentLi =
                                '<li class="list-group-item">' +
                                '<div class="row">' +
                                    '<div class="col-md-10">' +
                                    '<p id="comment-text-' + result.commentid + '">' + commentText + '</p>' +
                                    '</div>' +
                                    '<div class="col-md-2 text-right">' +
                                    '<span class="label label-standard label-tooltip" data-tooltip="' + result.username + '">' + result.userinitials + '</span> ' +
                                    '<span class="label label-standard label-tooltip" data-tooltip="' + result.created + '"><span class="glyphicon glyphicon-time"></span></span> ' +
                                    '<button class="btn btn-default btn-xs btn-edit-comment" data-id="' + result.commentid + '"><span class="glyphicon glyphicon-pencil opacity35"></span></button> ' +
                                    '</div>' +
                                '</div>' +
                                '</li>';

                            var $newCommentLi = $('#new-comment-li-' + issueID);
                            $newCommentLi.before(commentLi);

                            // close the new comment li
                            $newCommentLi.addClass('hidden');
                            // and clear the text box
                            $newCommentBox.val("");
                        }
                        else {
                            // something went wrong server-side
                            console.log(result);
                        }
                    },
                    error   : function(error) {

                    }
                })
            }
            else {
                // the fools didn't enter any text
            }
        },
        cancelAddComment: function() {
            var issueID = $(this).data('issueid');
            // hide the new comment li
            $('#new-comment-li-' + issueID).addClass('hidden');
            // clear any text which was typed into the box before cancel
            $('#new-comment-' + issueID).val("");
        },
        editAssociations: function() {

            // get the type of association we're changing
            var type = $(this).data('type');

            // get the issue number
            var issueID = $(this).data('issue');

            // hide all the associations divs
            $('.associations-div').addClass('hidden');
            // and unhide the correct one
            $('#' + type + '-div').removeClass('hidden');
            // change the title
            $('#associations-dialog-label').text('Edit ' + type);

            $('#' + type).tagsinput('removeAll');
            // populate the items which are already associated
            $('#issue-' + issueID).find('.association-' + type).each(function() {
                if (type == "locations") {
                    $('#' + type).tagsinput('add', { "value" : this.getAttribute('data-id'), "text": this.innerHTML, "region" : this.getAttribute('data-region')})
                }
                else {
                    $('#' + type).tagsinput('add', { "value" : this.getAttribute('data-id'), "text": this.innerHTML})
                }
            });

            // set the data for the save function
            $('#association-data').attr('data-type', type).attr('data-issue', issueID);

            // show the modal
            $('#associations-dialog').modal('show');
        },
        updateStatus: function() {

            // get the issue id and the status type
            var issueID = this.getAttribute('data-id');
            var status = this.getAttribute('data-status');

            // post these to the controller
            $.ajax({
                type    : 'POST',
                url     : 'dashboard/update_status',
                dataType: 'json',
                data    : {
                    'id'        : issueID,
                    'status'    : status
                },
                success : function(result) {

                    if (result.result == '1') {

                        // it worked, update the button text and class
                        var buttonText, buttonClass;
                        switch (status) {
                            case 'progress':
                                buttonText = "In Progress";
                                buttonClass = 'btn btn-xs btn-info dropdown-toggle';
                                break;
                            case 'awaiting':
                                buttonText = "Awaiting Response";
                                buttonClass = 'btn btn-xs btn-warning dropdown-toggle';
                                break;
                            case 'completed':
                                buttonText = 'Completed';
                                buttonClass = 'btn btn-xs btn-success dropdown-toggle';
                        }

                        buttonText += ' <span class="caret"></span>';
                        $('#btn-status-' + issueID)
                            .html(buttonText)
                            .removeClass()
                            .addClass(buttonClass);
                    }
                    else {
                        // server says no
                        console.log(result);
                    }
                },
                error   : function(error) {

                }
            });

            // don't follow the link
            event.preventDefault();
        },
        editTitle : function() {

            // get the issue id and the current title
            var issueID = $(this).attr('data-issueid');
            var currentTitle = $('#issue-' + issueID).attr('data-title');

            // set the dialog up for a title edit
            $('#edit-dialog-label').html('Edit Title');
            $('.edit-div').addClass('hidden');
            $('#title-div').removeClass('hidden');
            $('#title').val(currentTitle);
            $('#btn-save-edit').attr('data-type', 'title').attr('data-id', issueID);

            // show the dialog
            $('#edit-dialog').modal('show');
        },
        editComment : function() {

            // get the comment id and the current text
            var commentID = $(this).attr('data-id');
            var currentComment = $('#comment-text-' + commentID).text();

            // set the dialog up for a title edit
            $('#edit-dialog-label').html('Edit Comment');
            $('.edit-div').addClass('hidden');
            $('#comment-div').removeClass('hidden');
            $('#comment').val(currentComment);
            $('#btn-save-edit').attr('data-type', 'comment').attr('data-id', commentID);

            // show the dialog
            $('#edit-dialog').modal('show');

        }
    };

    var associationsDialog = {

        cancel  : function() {
            $('#associations-dialog').modal('hide');
        },
        save    : function() {

            // get the issue number, association type and data
            var $assocData = $('#association-data');
            var issueID = $assocData.attr('data-issue');
            var type = $assocData.attr('data-type');
            var data = $('#' + type).val();
            var fullData = $('#' + type).tagsinput('items');

            // post this to the controller
            $.ajax({
                type    : 'POST',
                url     : 'dashboard/update_associations',
                dataType: 'json',
                data    : {
                    'id'        : issueID,
                    'type'      : associationsDialog.classNames[type],
                    'items'     : data
                },
                success : function(result) {

                    if (result.result == '1') {

                        // it worked, update the labels
                        var label;
                        var labelClass;
                        var dataRegion;

                        // clear the current labels for this issue/label type
                        var $assocDiv = $('#' + type + '-' + issueID);
                        $assocDiv.html('');

                        for (var i = 0; i < fullData.length; i++) {

                            if (type == 'locations') {
                                labelClass = fullData[i].region;
                                dataRegion = ' data-region="' + fullData[i].region + '"';
                            }
                            else {
                                labelClass = 'standard';
                                dataRegion = '';
                            }

                            label = '<span class="label label-' + labelClass + ' association-staff" data-id="' + fullData[i].value + '" ' + dataRegion + '>' + fullData[i].text + '</span>\n';

                            $assocDiv.append(label);
                        }

                        // close the dialog
                        associationsDialog.cancel();
                    }
                    else {
                        // server says no
                        console.log(result);
                    }
                },
                error   : function(error) {

                }
            })

        },
        classNames : {
            'tags'      : 'Tag',
            'locations' : 'Office',
            'staff'     : 'Staff'
        }
    };

    var editDialog = {

        save : function() {

            // work out what we're saving
            var type = $(this).attr('data-type');

            // get the id and the text
            var id = $(this).attr('data-id');
            var text = $('#' + type).val();

            $.ajax({
                type    : 'POST',
                url     : 'dashboard/save_text',
                dataType: 'json',
                data    : {
                    'id'        : id,
                    'text'      : text,
                    'type'      : type
                },
                success : function(result) {

                    if (result.result == '1') {

                        // it worked, update the comment/title
                        $('#' + type + '-text-' + id).html(text);

                        // close the dialog
                        $('#edit-dialog').modal('hide');

                    }
                    else {
                        // server says no
                        console.log(result);
                    }
                },
                error   : function(error) {

                }
            });

        }
    };

    var filters = {

        refreshItems : function() {

            // put the button in a loading state
            var $btn = $(this);
            $btn.button('loading');

            // status options
            var status;
            ($('#filter-progress').hasClass('active')) ? status = 't' : status = 'f';
            ($('#filter-awaiting').hasClass('active')) ? status += ',t' : status += ',f';
            ($('#filter-completed').hasClass('active')) ? status += ',t' : status += ',f';

            // date options
            var dateFrom = $('#filter-start-date').val(),
                dateTo = $('#filter-end-date').val();

            // label options
            var locations = $locationsFilter.val(),
                staff = $staffFilter.val(),
                tags = $tagsFilter.val(),
                operLocations = $('#btn-andor-locations').find('.dropdown-label').text(),
                operStaff = $('#btn-andor-staff').find('.dropdown-label').text(),
                operTags = $('#btn-andor-tags').find('.dropdown-label').text();

            // check to see if there are any label filters...bit of reverse logic
            var labelFilters = (locations.length == 0 && staff.length == 0 && tags.length == 0) ? 'FALSE' : 'TRUE';

            // sort by
            var sortBy = ($('#filter-touched').hasClass('active')) ? 'touched' : 'created';

            // post to the controller
            $.ajax({
                type    : 'POST',
                url     : 'dashboard/load_items',
                data    : {
                    'status'        : status,
                    'labelFilters'  : labelFilters,
                    'locations'     : locations,
                    'staff'         : staff,
                    'tags'          : tags,
                    'operLocations' : operLocations,
                    'operStaff'     : operStaff,
                    'operTags'      : operTags,
                    'dateFrom'      : dateFrom,
                    'dateTo'        : dateTo,
                    'sortBy'        : sortBy
                },
                success : function(result) {

                    // it worked, update the items div
                    $('#items').html(result);

                    // reset the button
                    $btn.button('reset');
                },
                error   : function(error) {

                }
            });
        },
        andOrChange : function() {

            // change the button text
            $(this).parents('.input-group-btn').find('.dropdown-label').text($(this).attr('data-andor'));

            // don't follow the link
            event.preventDefault();
        },
        dateRangeChange : function() {

            // pointers for the inputs
            var $startDate = $('#filter-start-date'),
                $endDate = $('#filter-end-date');

            // get the date range we're working with
            var dateRange = $(this).attr('data-range');

            if (dateRange == 'tm') {

                // set start date to the 1st of the month
                $startDate.datepicker('update', moment().startOf('month').format('DD/MM/YYYY'));
                // set end date to today
                $endDate.datepicker('update', moment().format('DD/MM/YYYY'));

            }

            if (dateRange == 'l7d') {

                // set the start date to 7 days ago
                $startDate.datepicker('update', moment().subtract('days', 7).format('DD/MM/YYYY'));
                // set end date to today
                $endDate.datepicker('update', moment().format('DD/MM/YYYY'));
            }

            if (dateRange == 'l30d') {

                // set the start date to 7 days ago
                $startDate.datepicker('update', moment().subtract('days', 30).format('DD/MM/YYYY'));
                // set end date to today
                $endDate.datepicker('update', moment().format('DD/MM/YYYY'));
            }

            if (dateRange == 'all') {

                // clear both the start and end date
                $startDate.val('');
                $endDate.val('');
            }

            // don't follow the link
            event.preventDefault();
        }

    };

    // ~~~~~~~~~~~~~~~
    // event listeners
    // ~~~~~~~~~~~~~~~

    var $body = $('body');

    $body.on('click','.btn-add-new-comment', issuePanel.addComment);
    $body.on('click','.btn-save-new-comment', issuePanel.saveComment);
    $body.on('click','.btn-cancel-new-comment', issuePanel.cancelAddComment);
    $body.on('click','.btn-edit-associations', issuePanel.editAssociations);
    $body.on('click','.btn-issue-status', issuePanel.updateStatus);
    $body.on('click','.btn-edit-title', issuePanel.editTitle);
    $body.on('click','.btn-edit-comment', issuePanel.editComment);
    $body.on('click','#btn-save-edit', editDialog.save);
    $body.on('click','#btn-refresh', filters.refreshItems);
    $body.on('click','.btn-andor-choose', filters.andOrChange);
    $body.on('click','.btn-date-range', filters.dateRangeChange);

    var $ad = $('#associations-dialog');

    $ad.on('click', '#btn-cancel-associations', associationsDialog.cancel);
    $ad.on('click', '#btn-save-associations', associationsDialog.save);

});
