function toggleTaskStatus(status, user_id, task_id) {
    if (status == 'closed') {
        $confirm = $jsLang['alert_end_task'];
    } else {
        $confirm = $jsLang['alert_open_task'];
    }
    if (!confirm($confirm)) {
        return false;
    }
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/toggleTaskStatus",
        data: {
            status: status,
            user_id: user_id,
            task_id: task_id
        },
        success: function(response) {
            console.log(response);
            location.reload();
        }
    });
}

function unassign(user_id, task_id) {
    if (!confirm($jsLang['alert_unassign_task'])) {
        return false;
    }
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/unassignTaskToUser",
        data: {
            user_id: user_id,
            task_id: task_id
        },
        success: function(response) {
            // console.log(response);
            location.reload();
        }
    });
}

function filterUserByType(filterId, filterType) {
    $diffId = $("#selTreatment :selected").val();
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/filterUserByType",
        data: {
            filterId: filterId,
            filterType: filterType,
            diffId: $diffId
        },
        success: function(response) {
            $('#contentArea').html(response);
            if (filterType == 'treatment') {
                generateSelProblem(filterId);
            }
        }
    });
}

function generatexls() {
    $difficulty_id = $('#selTreatment :selected').val();
    $problem_id = $('#selProblem :selected').val();
    $forceDownload_URL = $sitePath + "/minapp/generatexls";
    $forceExport_URL = $sitePath + "/minapp/exportxls";
    $.ajax({
        url: $forceDownload_URL,
        type: 'post',
        dataType: "json",
        data: {
            difficulty_id: $difficulty_id,
            problem_id: $problem_id
        },
        beforeSend: function() {
            $("#preLoader").show();
        },
        success: function(response) {
            var str_json = JSON.stringify(response);
            $.download($forceExport_URL, 'filename=stats&format=xls&content=' + encodeURIComponent(str_json));
            $("#preLoader").hide();
        }
    });
}


function generateSelProblem(newVal) {
    $path = $sitePath + "/minapp/getProblemOptions";
    $.ajax({
        url: $path,
        type: 'post',
        data: {
            newVal: newVal
        },
        success: function(resp) {
            rs = '<option value="0">' + $jsLang['sel_problem'] + '</option>' + resp;
            $('#selProblem').html(rs);
        },
        error: function() {
            console.log('error');
        }
    });
}

function filterProblem(filterId) {
    var diffId = $('#selTreatment :selected').val();
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/filterProblem",
        async: true,
        data: {
            diffId: diffId,
            filterId: filterId
        },
        success: function(response) {
            $('#contentArea').html(response);
        }
    });
}

function view(username) {
    $path = $sitePath + "/minapp/view/" + username;
    location.href = $path;
}

function assignToUser(userId, username, diffId) {
    $(document)[0].title = 'BIP Admin Panel';
    $path = $sitePath + "/minapp/assignToUserForm";
    $.ajax({
        url: $path,
        type: 'post',
        data: {
            user_id: userId,
            username: username,
            diffId: diffId
        },
        success: function(response) {
            $('#innercontentArea').html(response);
            $("#frmAssignTask").validate({
                rules: {
                    problem_id: {
                        required: true
                    },
                    task_id: {
                        required: true
                    }
                },
                messages: {
                    problem_id: {
                        required: $jsLang['required']
                    },
                    task_id: {
                        required: $jsLang['required']
                    },
                }
            });
        }
    });
}

function changeReminderSettings(userId, username, diffId) {
    $(document)[0].title = 'BIP Admin Panel';
    $path = $sitePath + "/minapp/changeReminderSettings";
    $.ajax({
        url: $path,
        type: 'post',
        data: {
            user_id: userId,
            username: username,
            diffId: diffId
        },
        success: function(response) {
            $('#contentArea').html(response);
        }
    });
}

function fillDiffFormReminder(app_reminder_type) {

    var addmsgHtml = '<span class="addmsg"><a href="#" id="addScnt"  style="width:100%; float:left;">[+] Lägg till påminnelse</a></span>',
        formData = $('#frmReminder').serializeArray();

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/minapp/fillDiffFormReminder",
        dataType: 'html',
        data: formData,
        success: function(response) {
            $('#rem_scents').html('').html(response);

            if (app_reminder_type > 0) $("#rem_scents").append(addmsgHtml);

            $('.reminder_time').timepicker({
                minutes: {
                    interval: 10
                },
                showPeriodLabels: false,
                showNowButton: true,
                showDeselectButton: false,
                defaultTime: '', // removes the highlighted time for when the input is empty.
                showCloseButton: true
            });
        }
    });
}

function savePushReminder() {

    var formData = $('#frmReminder').serializeArray(),
        frm_count = $('#rem_scents li').size();

    formData.push({
        name: "frm_count",
        value: frm_count
    });

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/minapp/savePushReminder",
        data: formData,
        dataType:'json',
        success: function(response) {
          $("#frmReminder .error_msg").remove();
            if (response.success) {
              $.facebox($jsLang['saved_successfully']);
              $('#facebox').delay($fadeOutTime).fadeOut();
              $('#facebox_overlay').delay(500).fadeOut();
            }else{
              alert(response.error);return false;
            }
        }
    });
}

function assignTaskToUser(userId) {
    $(document)[0].title = 'BIP Admin Panel';
    $username = $('#username').val();
    $path = $sitePath + "/minapp/assignTaskToUser";
    var str = $("#frmAssignTask").serialize();
    $.ajax({
        url: $path,
        type: 'post',
        data: str,
        beforeSend: function() {
            if (!$("#frmAssignTask").valid()) {
                $("#preLoader").hide();
                return false;
            }
        },
        success: function(response) {
            // console.log(response);return false;
            view($username);
        }
    });
}

function addTaskToMobile(userId, username, diffId) {
    $(document)[0].title = 'BIP Admin Panel';
    $path = $sitePath + "/minapp/addTaskToMobileForm";
    $.ajax({
        url: $path,
        type: 'post',
        data: {
            user_id: userId,
            username: username,
            diffId: diffId
        },
        success: function(response) {
            $('#innercontentArea').html(response);
        }
    });
}

function addTaskByPatient(userId) {
    $(document)[0].title = 'BIP Admin Panel';

    $username = $('#username').val();

    if ($("#selProblem :selected").val() <= 0) {
        $("#selProblem").focus();
        alert($jsLang['no_problem_sel']);
        return false;
    }

    if ($('#taskgr').is(':checked')) {
        if ($("#selTask :selected").val() <= 0) {
            $("#selTask").focus();
            alert($jsLang['no_task_sel']);
            return false;
        }
    } else {
        if ($('#cinputs').val().length == 0) {
            $('#cinputs').focus();
            alert($jsLang['task_input']);
            return false;
        }
        /*var checked = $("#frmAddTask input[type=checkbox]:checked").length > 0;
        if (!checked) {
            alert("Please check at least one checkbox");
            return false;
        }*/
    }

    $path = $sitePath + "/minapp/addTaskByPatient";
    var str = $("#frmAddTask").serialize();
    $.ajax({
        url: $path,
        type: 'post',
        data: str,
        success: function(response) {
            if (response) {
                view($username);
            } else {
                console.log(response);
                alert('Task Add Error');
                return false;
            }
        }
    });
}


//Added by Sabin @ 3rd April 2015 Start >>
function showRegistrationTaskList(userId, username, diffId) {
    $(document)[0].title = 'BIP Admin Panel';
    $path = $sitePath + "/minapp/showRegistrationTaskList";
    $.ajax({
        url: $path,
        type: 'post',
        data: {
            user_id: userId,
            username: username,
            diffId: diffId
        },
        success: function(response) {
            $('#contentArea').html(response);
        }
    });
}
//Added by Sabin @ 3rd April 2015 End <<

//Added by Sabin @ 22nd June 2015 Start >>
function showHomeworkList(userId, username, diffId) {
    $(document)[0].title = 'BIP Admin Panel';
    $path = $sitePath + "/minapp/showHomeworkList";
    $.ajax({
        url: $path,
        type: 'post',
        data: {
            user_id: userId,
            username: username,
            diffId: diffId
        },
        success: function(response) {
            $('#contentArea').html(response);
        }
    });
}

function showActivationModulesList(userId, username, diffId) {
    $(document)[0].title = 'BIP Admin Panel';
    $path = $sitePath + "/minapp/showActivationModulesList";
    $.ajax({
        url: $path,
        type: 'post',
        data: {
            user_id: userId,
            username: username,
            diffId: diffId
        },
        success: function(response) {
            $('#contentArea').html(response);
        }
    });
}

function showCrisisplanList(userId, username, diffId) {
    $(document)[0].title = 'BIP Admin Panel';
    $path = $sitePath + "/minapp/showCrisisplanList";
    $.ajax({
        url: $path,
        type: 'post',
        data: {
            user_id: userId,
            username: username,
            diffId: diffId
        },
        success: function(response) {
            $('#contentArea').html(response);
        }
    });
}
//Added by Sabin @ 22nd June 2015 End <<
