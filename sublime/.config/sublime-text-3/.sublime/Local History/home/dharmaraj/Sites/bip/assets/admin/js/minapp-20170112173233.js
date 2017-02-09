function showDblRatingSlide() {
    alert('chaged');
    return false;
}

function showBox() {
    $('#selhide').fadeIn('fast');
}

function hideBox() {
    $('#selhide').fadeOut('fast');
}

function savePushNotification() {
    var str = $('#frmSetNotify').serialize();
    var diffId = $('#selDiff1 :selected').val();
    $serializeData = str + '&diffId=' + diffId;

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/savePushNotification",
        data: $serializeData,
        dataType:'json',
        success: function(response) {
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

function savePushReminder() {

    var formData = $('#frmReminder').serializeArray(),
        frm_count = $('#rem_scents li').size();

    formData.push({
        name: "frm_count",
        value: frm_count
    });

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/savePushReminder",
        data: formData,
        dataType:'json',
        success: function(response) {
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

function listPaginatedTasks(orderBy, offset) {
    if (orderBy == undefined) {
        orderBy = 'desc';
    }
    if (offset == undefined) {
        offset = 0;
    }

    var postData = {};
    postData.offset = offset;
    postData.orderBy = orderBy;
    // postData.diffId = $('#diffId').val();
    postData.diffId = $("#box-tasks-view #diffId").val();
    // postData.filterId = $('#filterId').val();
    postData.filterId = $("#box-tasks-view #filterId").val();
    // postData.filterType = $('#filterType').val();
    postData.filterType = $("#box-tasks-view #filterType").val()
    // console.log(postData);

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/listAllTasks",
        data: postData,
        success: function(response) {
            $('#box-tasks').html(response);
        }
    });
}

function listTasksSelectedOnly() {
    showBox();
    $("#regfilter").hide();
    var diffId = $('#selhide #selTreatment1 :selected').val();
    var filterId = ($("#selhide #selProblem1 :selected").val() != 0) ? $('#selhide #selProblem1 :selected').val() : diffId;
    var filterType = ($("#selhide #selProblem1 :selected").val() != 0) ? 'problem' : 'treatment';
    var offset = 0;
    var orderBy = 'desc';

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/listAllTasks",
        data: {
            offset: offset,
            orderBy: orderBy,
            diffId: diffId,
            filterId: filterId,
            filterType: filterType
        },
        success: function(response) {
            $('#box-tasks').html(response);
        }
    });
}

function filterTask(orderBy, offset, filterId, filterType) {

		var diffId;

		if (filterType==='treatment')
    	diffId = filterId
		else
    	diffId = $("#box-tasks-view #diffId").val()

    if (offset == undefined) {
        offset = 0;
    }
    if (orderBy == undefined) {
        orderBy = 'desc';
    }
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/listAllTasksAjax",
        data: {
            offset: offset,
            orderBy: orderBy,
            diffId: diffId,
            filterId: filterId,
            filterType: filterType
        },
        success: function(response) {
            $("#box-tasks-view #diffId").val(diffId)
            if (filterType === 'treatment') {
                generateSelProblem(filterId);
            }
            if (filterType == 'problem' && filterId == '0') {
                generateSelTreatment();
            }
            $('#box-tasks-view').html(response);
        }
    });
}

function populate(frm, data) {
    $.each(data, function(key, value) {
        $('[name=' + key + ']', frm).val(value);
    });
}

function fillDiffForm(diffId) {
    var diffId = $('#selDiff :selected').val();
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/fillDiffForm",
        dataType: 'json',
        data: {
            diffId: diffId
        },
        success: function(response) {
            populate('#frmSetTreatment', response);
        }
    });
}

function fillTreatmentForm(diffId) {
    var diffId = $('#selDiff :selected').val();
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/fillTreatmentForm",
        dataType: 'json',
        data: {
            diffId: diffId
        },
        success: function(response) {
            $('form').each(function() {
                $('#icon_div img').remove();
                clearForm(this);
                $(this).loadJSON(response);
                $('#uploaded_file').hide();
                var urlcss = $sitePath.replace("index.php", "");
                if (response.slide3_image) {
                    image = '<img style="width:100px;hieght:100px;" src="' + urlcss + 'images/uploads/app_images/' + response.slide3_image + '">';
                    $("#icon_div").html(image);
                } else {
                    $('#slide3_image').val();
                }
            });
            var selectedVal = "";
            var selected = $("input[type='radio'][name='rating']:checked");
            if (selected.length > 0) {
                selectedVal = selected.val();
                $("div.desc").hide();
                $("#rating" + selectedVal).show();
            }
        }
    });
}

function fillDiffFormNotify(diffId) {
    var diffId = $('#selDiff1 :selected').val();
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/fillDiffFormNotify",
        dataType: 'json',
        data: {
            diffId: diffId
        },
        success: function(response) {
            $('#frmSetNotify')[0].reset();
            $('#frmSetNotify').loadJSON(response);
            if (diffId != '0') {
                $('.togglebx').removeClass('lblnotify2').addClass('lblnotify1');
                $('.hidePush').show();
            } else {
                $('.togglebx').removeClass('lblnotify1').addClass('lblnotify2');
                $('.hidePush').hide();
            }
        }
    });
}

function fillDiffFormReminder(diffId) {

    var addmsgHtml = '<span class="addmsg"><a href="#" id="addScnt"  style="width:100%; float:left;">[+] Lägg till påminnelse</a></span>';

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/fillDiffFormReminder",
        dataType: 'html',
        data: {
            diffId: diffId
        },
        success: function(response) {
            $('#rem_scents').html('').html(response);
            $("#rem_scents").append(addmsgHtml);
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

function clearForm(form) {
    // iterate over all of the inputs for the form
    // element that was passed in
    $(':input', form).each(function() {
        var type = this.type;
        var tag = this.tagName.toLowerCase(); // normalize case
        // it's ok to reset the value attr of text inputs,
        // password inputs, and textareas
        if (type == 'text' || type == 'password' || tag == 'textarea')
            this.value = "";
        // checkboxes and radios need to have their checked state cleared
        // but should *not* have their 'value' changed
        else if (type == 'checkbox' || type == 'radio')
            this.checked = false;
        // select elements need to have their 'selectedIndex' property set to -1
        // (this works for both single and multiple select elements)
        else if (tag == 'select')
            this.selectedIndex = -1;
    });
};

function generateSelProblem(newVal) {
    $path = $sitePath + "/minapp/admin/getProblemOptions";
    $.ajax({
        url: $path,
        type: 'post',
        data: {
            newVal: newVal
        },
        success: function(resp) {
            rs = '<option value="0">' + $jsLang['sel_problem'] + '</option>' + resp;
            $('#selProblem1').html(rs);
        },
        error: function() {
            console.log('error');
        }
    });
}

function generateSelTreatment() {
    $path = $sitePath + "/minapp/admin/getTreatmentOptions";
    $.ajax({
        url: $path,
        type: 'post',
        data: {},
        success: function(resp) {
            rs = '<option value="0">' + $jsLang['sel_choose_treat'] + '</option>' + resp;
            $('#selTreatment1').html(rs);
        },
        error: function() {
            console.log('error');
        }
    });
}

function addTasksForm() {
    hideBox();
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/addTasksForm",
        async: false,
        data: {},
        success: function(response) {
            $('#box-tasks').html(response);
            myInitChecklistFunction();
            $("#frmAddTask").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    'problem_id[]': {
                        required: true
                    },
                    'tag[]': {
                        required: true
                    },
                    task: {
                        required: true
                    }

                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    'problem_id[]': {
                        required: $jsLang['required']
                    },
                    'tag[]': {
                        required: $jsLang['required']
                    },
                    task: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}

function addTreatmentsForm() {
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/addTreatmentsForm",
        async: false,
        data: {},
        success: function(response) {
            $('#box-category').html(response);
            $("#frmAddTreatment").validate({
                rules: {
                    difficulty: {
                        required: true
                    },
                    problem: {
                        required: true
                    }

                },
                messages: {
                    difficulty: {
                        required: $jsLang['required']
                    },
                    problem: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}

function setTreatmentForm() {
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/setTreatmentForm",
        async: false,
        data: {},
        success: function(response) {
            $('#box-settings').html(response);
        }
    });
}



function addTask() {
    hideBox();
    $(document)[0].title = 'BIP Admin Panel';
    var str = $("#frmAddTask").serialize();
    $.ajax({
        type: "Post",
        url: $sitePath + "/minapp/admin/addTask",
        data: str,
        dataType:'json',
        async: false,
        beforeSend: function() {
            if (!$("#frmAddTask").valid()) {
                $("#preLoader").hide();
                return false;
            }
        },
        success: function(response) {
            if (response==true){
                listTasksSelectedOnly();
            }else{
                alert(response);return false;
            }
        },
        error: function() {
            console.log("error");
        }

    });
}


function addTreatment() {
    $(document)[0].title = 'BIP Admin Panel';
    var str = $("#frmAddTreatment").serialize();
    $.ajax({
        type: "Post",
        url: $sitePath + "/minapp/admin/addTreatment",
        data: str,
        forceSync: true,
        async: false,
        beforeSend: function() {
            if (!$("#frmAddTreatment").valid()) {
                $("#preLoader").hide();
                return false;
            }
        },
        success: function(response) {
            $.facebox($jsLang['saved_successfully']);
            $('#facebox').delay($fadeOutTime).fadeOut();
            $('#facebox_overlay').delay(500).fadeOut();
            $('#box-category').html(response);
        }
    });
}

function validateFrm(frm) {
    switch (frm.name) {
        case 'frmSlide_1':
            $(frm).validate({
                rules: {
                    anxiety: {
                        required: true
                    },
                    zero: {
                        required: true
                    },
                    ten: {
                        required: true
                    },
                    txt_button: {
                        required: true
                    },
                },
                messages: {
                    anxiety: {
                        required: $jsLang['required']
                    },
                    zero: {
                        required: $jsLang['required']
                    },
                    ten: {
                        required: $jsLang['required']
                    },
                    txt_button: {
                        required: $jsLang['required']
                    },
                }
            });
            break;

        case 'frmSlide1':
            $(frm).validate({
                rules: {
                    slide1_headline: {
                        required: true
                    },
                    slide1_text: {
                        required: true
                    },
                    slide1_button: {
                        required: true
                    },
                },
                messages: {
                    slide1_headline: {
                        required: $jsLang['required']
                    },
                    slide1_text: {
                        required: $jsLang['required']
                    },
                    slide1_button: {
                        required: $jsLang['required']
                    },
                }
            });
            break;

        case 'frmSlide2':
            $(frm).validate({
                rules: {
                    slide2_zero: {
                        required: true
                    },
                    slide2_ten: {
                        required: true
                    },
                    slide2_button: {
                        required: true
                    },
                },
                messages: {
                    slide2_zero: {
                        required: $jsLang['required']
                    },
                    slide2_ten: {
                        required: $jsLang['required']
                    },
                    slide2_button: {
                        required: $jsLang['required']
                    },
                }
            });
            break;

        case 'frmSlide3':
            $(frm).validate({
                rules: {
                    slide3_image: {
                        required: true
                    },
                    slide3_text: {
                        required: true
                    },
                    slide3_button: {
                        required: true
                    },
                },
                messages: {
                    slide3_image: {
                        required: $jsLang['required']
                    },
                    slide3_text: {
                        required: $jsLang['required']
                    },
                    slide3_button: {
                        required: $jsLang['required']
                    },
                }
            });
            break;

        case 'frmSlide4':
            $(frm).validate({
                rules: {
                    slide4_zero: {
                        required: true
                    },
                    slide4_ten: {
                        required: true
                    },
                    slide4_button: {
                        required: true
                    },
                },
                messages: {
                    slide4_zero: {
                        required: $jsLang['required']
                    },
                    slide4_ten: {
                        required: $jsLang['required']
                    },
                    slide4_button: {
                        required: $jsLang['required']
                    },
                }
            });
            break;

        case 'frmSlide5':
            $(frm).validate({
                rules: {
                    slide5_time_x: {
                        required: true
                    },
                    slide5_time_y: {
                        required: true
                    },
                    slide5_time_text1: {
                        required: true
                    },
                    slide5_time_text2: {
                        required: true
                    },
                    slide5_time_text3: {
                        required: true
                    },
                    slide5_button: {
                        required: true
                    },
                },
                messages: {
                    slide5_time_x: {
                        required: $jsLang['required']
                    },
                    slide5_time_y: {
                        required: $jsLang['required']
                    },
                    slide5_time_text1: {
                        required: $jsLang['required']
                    },
                    slide5_time_text2: {
                        required: $jsLang['required']
                    },
                    slide5_time_text3: {
                        required: $jsLang['required']
                    },
                    slide5_button: {
                        required: $jsLang['required']
                    },
                }
            });
            break;

    }
}

function validateCustomFrm(frm) {
    $(frm).validate({
        rules: {
            cancel_message: {
                required: true
            }
        },
        messages: {
            cancel_message: {
                required: $jsLang['required']
            }
        }
    });
}

function setTreatment(frm) {
    $(document)[0].title = 'BIP Admin Panel';
    var formData = $(frm).serializeArray();

    if (frm.name === 'frmSlide6') {
        var frm_count = $('#p_scents p').size();
        formData.push({
            name: "frm_count",
            value: frm_count
        });
    } else {
        var difficulty = $('#selDiff :selected').val();
        if (!difficulty) {
            var difficulty = $('#altDiff').val();
        }
        if (!difficulty) {
            alert('Vänligen välj behandling');
            return false;
        };
        formData.push({
            name: "difficulty",
            value: difficulty
        });
    }

    $.ajax({
        type: "post",
        url: $sitePath + "/minapp/admin/setTreatment",
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $("#preLoader").hide();
        },
        success: function(response) {
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

function setCustomMessage(frm) {
    $(document)[0].title = 'BIP Admin Panel';
    var formData = $(frm).serializeArray();
    $.ajax({
        type: "post",
        url: $sitePath + "/minapp/admin/setCustomMessage",
        data: formData,
        dataType:'json',
        beforeSend: function() {
            validateCustomFrm(frm);
            if (!$(frm).valid()) {
                $("#preLoader").hide();
                return false;
            }
            $("#preLoader").hide();

        },
        success: function(response) {
            if (response===true) {
                $.facebox($jsLang['saved_successfully']);
                $('#facebox').delay($fadeOutTime).fadeOut();
                $('#facebox_overlay').delay(500).fadeOut();
            }else{
                alert(response);return false;
            }
        }
    });
}

function listAllTreatmentSettings() {
    $('#selhide').hide();
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/listAllTreatmentSettings",
        success: function(response) {
            $('#box-settings').html(response);
        }
    });
}

function listPushNotify() {
    $('#selhide').hide();
    $('.hidePush').hide();
}

function listTasks() {
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/listAllTasks",
        success: function(response) {
            $('#box-tasks').html(response);
        }
    });
}

function listTreatments() {
    $('#selhide').hide();
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/listAllTreatments",
        success: function(response) {
            $('#box-category').html(response);
        }
    });
}

function checkProblemInTasks(problem_id) {
    $path = $sitePath + "/minapp/admin/checkProblemInTasks";

    $.ajax({
        url: $path,
        type: 'post',
        data: {
            'problem_id': problem_id
        },
        success: function(response) {
            if (response) {
                alert($jsLang['link_with_tasks']);
            } else {
                if (confirm($jsLang['sure_del_problem']))
                    deleteProblem(problem_id);
            }
        }

    });
}

function checkTaskInUser(task_id) {
    $path = $sitePath + "/minapp/admin/checkTaskInUser";

    $.ajax({
        url: $path,
        type: 'post',
        data: {
            'task_id': task_id
        },
        success: function(response) {
            if (response) {
                alert($jsLang['link_with_trainings']);
                return false;
            } else {
                if (confirm($jsLang['sure_del_task']))
                    deleteTask(task_id);
            }
        }

    });
}

function deleteTreatmentSetting(treatment_id) {
    $(document)[0].title = 'BIP Admin Panel';
    if (!confirm($jsLang['sure_del_treatment'])) {
        return;
    }
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/deleteTreatmentSetting",
        data: {
            "treatment_id": treatment_id
        },
        success: function(response) {
            $('#box-settings').html(response);
            $.facebox($jsLang['saved_successfully']);
            $('#facebox').delay($fadeOutTime).fadeOut();
            $('#facebox_overlay').delay(500).fadeOut();
        }
    });
}

function deleteProblem(problem_id) {
    $(document)[0].title = 'BIP Admin Panel';

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/deleteProblem",
        data: {
            "problem_id": problem_id
        },
        success: function(response) {
            $('#box-category').html(response);
            $.facebox($jsLang['saved_successfully']);
            $('#facebox').delay($fadeOutTime).fadeOut();
            $('#facebox_overlay').delay(500).fadeOut();
        }
    });
}

function deleteTask(task_id) {
    $(document)[0].title = 'BIP Admin Panel';

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/deleteTask",
        data: {
            "task_id": task_id
        },
        success: function(response) {
            listTasksSelectedOnly();
        }
    });
}

function editProblem(problem_id) {
    $path = $sitePath + "/minapp/admin/editProblem";
    $(document)[0].title = 'BIP Admin Panel';

    $.ajax({
        type: 'post',
        url: $path,
        data: {
            problem_id: problem_id
        },
        success: function(response) {
            $('#box-category').html(response);
            $("#frmAddTreatment").validate({
                rules: {
                    difficulty: {
                        required: true
                    },
                    problem: {
                        required: true
                    }

                },
                messages: {
                    difficulty: {
                        required: $jsLang['required']
                    },
                    problem: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}

function editFeedbackMessage(treatment_id) {
    $path = $sitePath + "/minapp/admin/editFeedbackMessage";
    $(document)[0].title = 'BIP Admin Panel';

    $.ajax({
        type: 'post',
        url: $path,
        data: {
            treatment_id: treatment_id
        },
        success: function(response) {
            $('#box-settings').html(response);
        }
    });
}

function editTreatmentSetting(treatment_id) {
    $path = $sitePath + "/minapp/admin/editTreatmentSetting";
    $(document)[0].title = 'BIP Admin Panel';

    $.ajax({
        type: 'post',
        url: $path,
        data: {
            treatment_id: treatment_id
        },
        success: function(response) {
            $('#box-settings').html(response);
            $("#frmSetTreatment").validate({
                rules: {
                    difficulty: {
                        required: true
                    },
                    anxiety: {
                        required: true
                    },
                    zero: {
                        required: true
                    },
                    ten: {
                        required: true
                    }
                },
                messages: {
                    difficulty: {
                        required: $jsLang['required']
                    },
                    anxiety: {
                        required: $jsLang['required']
                    },
                    zero: {
                        required: $jsLang['required']
                    },
                    ten: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}

function editTask(task_id) {
    hideBox();
    $path = $sitePath + "/minapp/admin/editTask";
    $(document)[0].title = 'BIP Admin Panel';

    $.ajax({
        type: 'post',
        url: $path,
        data: {
            task_id: task_id
        },
        success: function(response) {
            $('#box-tasks').html(response);
            myInitChecklistFunction();
            $("#frmAddTask").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    'problem_id[]': {
                        required: true
                    },
                    'tag[]': {
                        required: true
                    },
                    task: {
                        required: true
                    }

                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    'problem_id[]': {
                        required: $jsLang['required']
                    },
                    'tag[]': {
                        required: $jsLang['required']
                    },
                    task: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}

/**
 * List all registration tasks
 * @author  Sabin Chhetri
 * @Date    24th March 2015
 * @return  nothing
 */
function listRegistrationTask() {
    //showBox();
    $("#selhide").hide();
    $("#regfilter").show();

    $("#map-custom-answers").parent("div").show();
    $("#manage-special-answers").parent("div").show();

    var diffId = $('#regfilter').find("#selTreatment1").val();
    var filterId = diffId;
    var filterType = 'treatment';
    var offset = 0;
    var orderBy = 'desc';


    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/listAllRegistrationTasks",
        data: {
            offset: offset,
            orderBy: orderBy,
            diffId: diffId,
            filterId: filterId,
            filterType: filterType
        },
        success: function(response) {
            $('#box-registrationtask').html(response);
            $("#map-custom-answers").show();
            $("#manage-special-answers").show();

        }
    });
}

/**
 * Function to show registration task form
 */
function addRegistrationTasksForm() {
    $("#regfilter").hide();
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/addRegistrationTasksForm",
        data: {},
        success: function(response) {
            $('#box-registrationtask').html(response);
            $("#reg_difficulty_id").dropdownchecklist( { emptyText: "<i>Select Difficulty</i>", width: 300 } );
            $("#frmAddRegistrationTask").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    flow_type: {
                        required: true
                    },
                    registration_name: {
                        required: true
                    }
                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    flow_type: {
                        required: $jsLang['required']
                    },
                    registration_name: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}

function addRegistrationTask() {
    hideBox();
    $(document)[0].title = 'BIP Admin Panel';
    var str = $("#frmAddRegistrationTask").serialize();
    $.ajax({
        type: "Post",
        url: $sitePath + "/minapp/admin/addRegistrationTask",
        data: str,
        async: false,
        beforeSend: function() {
            if (!$("#frmAddRegistrationTask").valid()) {
                $("#preLoader").hide();
                return false;
            }
        },
        success: function(response) {
            $r = $.parseJSON(response);
            if($r.error_code=="duplicate" || $r.error_code=='error'){
                alert($r.error_msg);
                return false;
            }else{
                listRegistrationTask();
            }
        },
        error: function() {
            console.log("error");
        }

    });
}


function toggleRegTaskStatus(obj,newStatus){
    if(typeof obj=="number"){
        var regID = obj;
    }else{
        var regID = obj.data("regid");
    }

    $.ajax({
        url: $sitePath + "/minapp/admin/changeRegistrationStatus",
        type: "POST",
        async:false,
        data: "registration_id="+regID+"&new_status="+newStatus,
        beforeSend:function(){
             $("#preLoader").show();
        },
        success:function(data){
            if(typeof obj=="number"){
                $("tr#ID_"+regID).fadeOut("slow",function(){
                    $(this).remove();
                })
            }else{
                $("#preLoader").hide();
                $status = newStatus==0?1:0;
                obj.attr("onclick","toggleRegTaskStatus($(this),"+$status+")");

                var d = $.parseJSON(data);
                obj.find("img").attr("src",decodeURIComponent(d.icon_path));
                obj.attr("title",d.tooltip);
            }

        }
    });
}


function deleteRegistrationTask(registration_id, inUse){
    var deleteReg = false;
    if(confirm("Are you sure you want to delete this registration task?")){
        if(inUse==true){
            if(confirm("This registration is already in use. Do you want to delete this?")){
                deleteReg = true;
            }
        }else{
            deleteReg = true;
        }

        if(deleteReg==true){ //now delete the registration
            toggleRegTaskStatus(parseInt(registration_id),0);
        }
    }
}

function editRegistrationTask(registration_id) {
    hideBox();
     $("#regfilter").hide();
    $path = $sitePath + "/minapp/admin/editRegistrationTask";
    $(document)[0].title = 'BIP Admin Panel';

    $.ajax({
        type: 'post',
        url: $path,
        data: {
            registration_id: registration_id
        },
        success: function(response) {
            $('#box-registrationtask').html(response);
            $("#reg_difficulty_id").dropdownchecklist( { emptyText: "<i>Select Difficulty</i>", width: 300 } );
            $("#frmAddRegistrationTask").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    flow_type: {
                        required: true
                    },
                    registration_name: {
                        required: true
                    }
                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    flow_type: {
                        required: $jsLang['required']
                    },
                    registration_name: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}


function filterRegistrationTask(orderBy, offset, filterId, filterType) {
    var diffId = $('#selTreatment1 :selected').val();
    if (offset == undefined) {
        offset = 0;
    }
    if (orderBy == undefined) {
        orderBy = 'desc';
    }
    var url, element;
    if($('#box-myhomework').is(":visible")){
        url = $sitePath + "/minapp/admin/listAllHomeworksAjax";
        element = "box-myhomework";
    }else if($('#box-crisisplan').is(":visible")){
        url = $sitePath + "/minapp/admin/listAllCrisisplansAjax";
        element = "box-crisisplan";
    }else{
        url = $sitePath + "/minapp/admin/listAllRegistrationTasksAjax";
        element = "box-registrationtask";
    }

    $.ajax({
        type: 'post',
        url: url,
        data: {
            offset: offset,
            orderBy: orderBy,
            diffId: diffId,
            filterId: filterId,
            filterType: filterType
        },
        success: function(response) {
            $('#'+element).html(response);
        }
    });
}

function listPaginatedRegistrationTasks(orderBy, offset) {
        if (orderBy == undefined) {
            orderBy = 'desc';
        }
        if (offset == undefined) {
            offset = 0;
        }

        var postData = {};
        postData.offset = offset;
        postData.orderBy = orderBy;
        postData.diffId = $('#diffId').val();
        postData.filterId = $('#filterId').val();
        postData.filterType = $('#filterType').val();
        console.log(postData);

        $.ajax({
            type: 'post',
            url: $sitePath + "/minapp/admin/listAllRegistrationTasksAjax",
            data: postData,
            success: function(response) {
                $('#box-registrationtask').html(response);
            }
        });
    }

    function showRegistrationFlow(registrationID){
        $("#selhide").hide();
        $("#regfilter").hide();
       /* var diffId = $('#selhide #selTreatment1 :selected').val();
        var filterId = ($("#selhide #selProblem1 :selected").val() != 0) ? $('#selhide #selProblem1 :selected').val() : diffId;
        var filterType = ($("#selhide #selProblem1 :selected").val() != 0) ? 'problem' : 'treatment';
        var offset = 0;
        var orderBy = 'desc';*/

        $.ajax({
            type: 'post',
            url: $sitePath + "/minapp/admin/listAddRegistrationFlow",
            data: "registration_id="+registrationID+"&offset = 0",
            success: function(response) {
                $('#box-registrationtask').html(response);
                 $("#frmAddRegistrationFlow").validate({
                    rules: {
                        flowpage_title: {
                            required: true
                        }
                    },
                    messages: {
                        flowpage_title: {
                            required: $jsLang['required']
                        }
                    }
                });
            }
        });
    }


    function addRegistrationFlow() {
        $(document)[0].title = 'BIP Admin Panel';
        var str = $("#frmAddRegistrationFlow").serialize();
        $.ajax({
            type: "Post",
            url: $sitePath + "/minapp/admin/addRegistrationFlow",
            data: str,
            async: false,
            beforeSend: function() {
                if (!$("#frmAddRegistrationFlow").valid()) {
                    $("#preLoader").hide();
                    return false;
                }
            },
            success: function(response) {
                showRegistrationFlow($("#frmAddRegistrationFlow").find("#registration_id").val());
            },
            error: function() {
                console.log("error");
            }

        });
}


function renameRegistrationFlow(flowid){
    var selector = $("#flow-list-table tbody tr").find("td[data-flowid='"+flowid+"']");
    selector.find(".flow-name").hide();
    selector.find(".flow-name-edit").show();
}

function editFlow(obj){
    var newFlowName = obj.prev("input").val();
    var flowid = obj.data("flowid");
    $.ajax({
        url: $sitePath + "/minapp/admin/editFlow",
        type: "POST",
        async: false,
        data: "new_flow_name="+newFlowName+"&flow_id="+flowid,
        beforeSend:function(){
            $("#preLoader").show();
        },
        success: function(data){
            $("#preLoader").hide();
            obj.parent().prev('.flow-name').html(newFlowName).show();
            obj.parent().hide();
        }
    });
}


function changeFlowStatus(obj,newStatus){


    var flowID = obj.data("flowid");
    $.ajax({
        url: $sitePath + "/minapp/admin/changeFlowStatus",
        type: "POST",
        async:false,
        data: "flow_id="+flowID+"&new_status="+newStatus,
        beforeSend:function(){
             $("#preLoader").show();
        },
        success:function(data){
            $("#preLoader").hide();
            $status = newStatus==0?1:0;
            obj.attr("onclick","changeFlowStatus($(this),"+$status+")");
            //obj.find("img").attr("src",$.trim(data));
            var d = $.parseJSON(data);
            obj.find("img").attr("src",decodeURIComponent(d.icon_path));
            obj.attr("title",d.tooltip);
        }
    });
}

function listPaginatedRegistrationFlows(orderBy, offset){
     if (orderBy == undefined) {
            orderBy = 'desc';
        }
        if (offset == undefined) {
            offset = 0;
        }

        var postData = {};
        postData.offset = offset;
        postData.orderBy = orderBy;
        postData.registration_id = $("#registration_id").val();


        $.ajax({
            type: 'post',
            url: $sitePath + "/minapp/admin/listAddRegistrationFlow",
            data: postData,
            success: function(response) {
                $('#box-registrationtask').html(response);
            }
        });
}

function listSteps(regID, flowID){
    $("#selhide").hide();
    $("#regfilter").hide();
    $.ajax({
        url: $sitePath + "/minapp/admin/listSteps",
        type: 'post',
        data: "registration_id="+regID+"&flow_id="+flowID,
        success:function(response){
            destroyTinyMCE();
            $('#box-registrationtask').html(response);
        }
    });
}

function showSelectedStep(registrationID,flowID,template,stepID){

    $('.hide-step-templates').removeClass("hide");
    $(".show-step-templates").hide();
    $.ajax({
        url: $sitePath + "/minapp/admin/getTemplatePage",
        type: "POST",
        data: "registration_id="+registrationID+"&flow_id="+flowID+"&template="+template+"&step_id="+stepID,
        beforeSend:function(){
            $("#preLoader").show();
        },
        success:function(data){
            $("#preLoader,.steps-holder").hide();
             destroyTinyMCE();
            $(".step-page-holder").empty().html(data).show();
        }
    });
}

function showHideSelectionOption(doWhat){

    if(doWhat==1){
         $(".choose-max-answers-holder").removeClass("hide");
         $("#max_selection_allowed").focus();
    }else{
        $(".choose-max-answers-holder").addClass("hide");
    }
}

function AddNewAnswer(){
    $answer = $("#step_answer").val();

    $test = $.inArray($answer.toLowerCase(),setArrayFromInput("answers[]"));
    if($test>-1){
        alert($jsLang["item_in_list"]);
        return false;
    }

    if($.trim($answer)!=""){
        $tableObj = $("#answer-list-table").find("tbody");
        $rowCount = $tableObj.find("tr").not(".no-answers").length;
        $newOrder = $rowCount+1;
        $html = "<tr data-row='row-answer' class='temp-row-"+$newOrder+"'>";
        $cls = "temp-row-"+$newOrder;
        $html +="<td class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>";
        $html +="<td class='td-answers handle'><input type='hidden' name='answers[]' class='inputs w100' value='"+$answer+"' /><span>"+$answer+"</span></td>";
        $html +="<td><a href='javascript:void(0)' class='remove-row' data-rowid='"+$cls+"' onclick='removeAnswerRow($(this))'><img src='../../images/admin_icons/delete.png'/></a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='edit-row' data-rowid='"+$cls+"' onclick='editAnswerRow($(this))'><img src='../../images/admin_icons/edit.png'/></a><input type='hidden' name='answer_cat_id[]' value='0' /><input type='hidden' name='answer_id[]' value='0' /><input type='hidden' name='answer_order[]' value='"+$newOrder+"' /></td>";
        $html +="</tr>";

        $tableObj.append($html);
        //update preview
        $(".phone-preview").find(".options-holder").append('<div class="step-answer temp-row-'+$newOrder+'"><label><input type="checkbox" value="1" class="selectoption" onclick="toggleOptionActive($(this))" /><span>'+$answer+'</span></label></div>');
        updatePreview();

        $(".no-answers").hide();


         $("#step_answer").val("").focus();
    }
}

function setArrayFromInput(arrayInput){
    var myArray = [];
    $("input[name='"+arrayInput+"']").each(function(){
        $val = $(this).val();
        myArray.push($val.toLowerCase());
    })
    return myArray;
}

function AddNewAnswerCat(){
    $answer_cat = $.trim($("#step_answer_cat").val());

    //to add answer category one should provide step title so that we can save category to database.
    if($.trim($("#step_title").val())=="" && $("#step_id").val()==0){
        alert($jsLang["specify_step_title"]);
        return false;
    }
    //check if same category already added.
    $test = $.inArray($answer_cat.toLowerCase(),setArrayFromInput("answers_category[]"));
    if($test>-1){
        alert($jsLang["item_in_list"]);
        return false;
    }

    if($.trim($answer_cat)!=""){
        //save step to the database first
        $.ajax({
            url: $sitePath + "/minapp/admin/saveAnswerCategory",
            data: $("#frmRegistrationSteps").serialize(),
            type: "POST",
            beforeSend:function(){ $("#preLoader").show();},
            success: function(data){
                var d = $.parseJSON(data);
                $("#preLoader").hide();
                if(parseInt(d.step_id)>0){
                    $("#step_id").val(d.step_id);
                    if(d.new_step===1){
                         $class = (d.sort_order%2==1)?"even":"odd";
                         $("#step-list-table").find("tbody  tr.no-steps").remove();
                         //$no_data_row.
                         $("#step-list-table").append("<tr id='ID_"+d.step_id+"' class='"+$class+" step-row'>" +
                                                 "<td class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>" +
                                                 "<td class='handle' data-stepid='"+d.step_id+"'>"+$("#step_title").val()+"</td>"+
                                                 "<td class='handle'>"+d.template_name+"</td>" +
                                                "<td><a href='javascript:void(0)' data-stepid='"+d.step_id+"' data-newstatus='0' class='link-green change-step-status' title='Change Status to Inactive'><img src='../../images/admin_icons/enabled.gif'></a></td>"+
                                                "<td><a href='javascript:void(0)' onclick=\"showSelectedStep('"+d.registration_id+"','"+d.flow_id+"','"+d.template+"',"+d.step_id+")\"><img src='../../images/admin_icons/edit.png'></a></td>"
                                                 );
                    }


                    $tableObj = $("#answer-cat-list-table").find("tbody");
                    $rowCount = $tableObj.find("tr").not(".no-answers").length;
                    //$newOrder = (d.sort_order>0)?d.sort_order: $rowCount+1;
                    $newOrder = d.cat_id;
                    $html = "<tr data-row='row-answer-cat' class='answer-row-cat-"+$newOrder+"'>";
                    $cls = "answer-row-cat-"+$newOrder;
                    $html +="<td class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>";
                    $html +="<td class='td-answers-cat handle'><input type='hidden' name='answers_category[]' class='inputs w100' value='"+$answer_cat+"' /><span>"+$answer_cat+"</span></td>";
                    $html +="<td><a href='javascript:void(0)' class='remove-row-cat' data-dowhat='1' data-rowid='"+$cls+"' onclick='removeAnswerRowCat($(this))'><img src='../../images/admin_icons/delete.png'/></a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='edit-row-cat' data-rowid='"+$cls+"' onclick='editAnswerRowCat($(this))'><img src='../../images/admin_icons/edit.png'/></a><input type='hidden' name='cat_id[]' value='"+d.cat_id+"' /><input type='hidden' name='answer_cat_order[]' value='"+d.cat_sort_order+"' /></td>";
                    $html +="</tr>";

                    $tableObj.append($html);

                    $(".phone-preview").find("#accordion").append('<h4 class="accordion-toggle active answer-row-cat-'+$newOrder+'"  onclick="toggleAccordion($(this))">'+$answer_cat+'</h4>'+
                              '<div class="accordion-content">'+
                                '<div class="options-holder keywords">'+
                                    '<div class="steps-answer-holder">'+
                                    '</div>'+
                                    '<div data-catid="'+d.cat_id+'" data-catrow="'+$cls+'" class="step-answer add-another-button hide frt">'+
                                        '<label><img src="../../images/admin_icons/plus_orange.png" width="18" / >'+$jsLang["add_answer"]+'</label>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="clear"></div>'+
                              '</div>');
                    $acc = $(".answer-row-cat-"+$newOrder);

                    $(".accordion-content").not($acc.next()).slideUp('fast');
                    $(".accordion-toggle").not($cls).removeClass("active");
                    $("h4."+$cls).addClass("active");
                    $acc.next().slideToggle("fast");

                    updatePreview();


                    $("#answer-cat-list-table").find(".no-answers").hide();

                    $("#answer_cat_selector").append("<option data-rowid='"+$cls+"' value='"+d.cat_id+"'>"+$answer_cat+"</option>");

                    $("#step_answer_cat").val("").focus();
                }

            }
        });


    }
}

function AddNewAnswerWithCat(){
    $answer_cat_id = $("#answer_cat_selector option:selected").val();
    $answer = $("#step_answer").val();
    $cat_row_id = $("#answer_cat_selector option:selected").attr("data-rowid");

    if($.trim($answer_cat_id)==""){
        alert($jsLang["select_answer_category"]);
        return false;
    }else if($.trim($answer)==""){
        alert($jsLang["specify_answer"]);
        return false;
    }else{

        $checkTableCat = $("#answer-for-catid-"+$answer_cat_id).length;
        if($checkTableCat==0){
            $newOrder=1;
            $cls = "temp-row-"+$answer_cat_id+"-"+$newOrder;
            $html ="<tr data-catid='temp-row-cat-"+$answer_cat_id+"'><td colspan='3'>";
            $html +="<table cellpadding='0' cellspacing='0' border='0' width='100%' class='ec_grid' id='answer-for-catid-"+$answer_cat_id+"'>";
            $html +="<thead><tr><td colspan='3'>"+$("#answer_cat_selector option:selected").text()+"</td></tr></thead>";
            $html +="<tbody>";
            $html += "<tr data-row='row-answer' class='"+$cls+"'>";
            $html +="<td width='15%' class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>";
            $html +="<td width='60%' class='td-answers handle'><input type='hidden' name='answers[]' class='inputs w100' value='"+$answer+"' /><span>"+$answer+"</span></td>";
            $html +="<td width='25%'><a href='javascript:void(0)' class='remove-row' data-rowid='"+$cls+"' onclick='removeAnswerRowEc($(this))'><img src='../../images/admin_icons/delete.png'/></a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='edit-row' data-rowid='"+$cls+"' onclick='editAnswerRow($(this))'><img src='../../images/admin_icons/edit.png'/></a><input type='hidden' name='answer_cat_id[]' value='"+$answer_cat_id+"' /><input type='hidden' name='answer_id[]' value='0' /><input type='hidden' name='answer_order[]' value='"+$newOrder+"' /></td>";
            $html +="</tr>";
            $html +="</tbody>";
            $html +="</table>";
            $html +="</td></tr>";
            $tableObj = $("#answer-list-table-ec").find("tbody.main").append($html);
            MakeAnswerTableSortable();
        }else{
            $tableObj = $("#answer-for-catid-"+$answer_cat_id).find("tbody");
            $rowCount = $tableObj.find("tr").not(".no-answers").length;
            $newOrder = $rowCount+1;
            $cls = "temp-row-"+$answer_cat_id+"-"+$newOrder;
            $html = "<tr data-row='row-answer' class='"+$cls+"'>";
            $html +="<td class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>";
            $html +="<td class='td-answers handle'><input type='hidden' name='answers[]' class='inputs w100' value='"+$answer+"' /><span>"+$answer+"</span></td>";
            $html +="<td><a href='javascript:void(0)' class='remove-row' data-rowid='"+$cls+"' onclick='removeAnswerRowEc($(this))'><img src='../../images/admin_icons/delete.png'/></a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='edit-row' data-rowid='"+$cls+"' onclick='editAnswerRow($(this))'><img src='../../images/admin_icons/edit.png'/></a><input type='hidden' name='answer_cat_id[]' value='"+$answer_cat_id+"' /><input type='hidden' name='answer_id[]' value='0' /><input type='hidden' name='answer_order[]' value='"+$newOrder+"' /></td>";
            $html +="</tr>";
            $html +="</tbody>";
            $html +="</table>";
            $html +="</td></tr>";
            $tableObj.append($html);
        }


        $acc = $("h4."+$cat_row_id);

        $(".accordion-content").not($acc.next()).slideUp('fast');
        $(".accordion-toggle").not($cat_row_id).removeClass("active");
        $acc.addClass("active");



        $acc.next().slideDown("fast");

        //update preview
        $acc.next(".accordion-content").find(".options-holder").find(".steps-answer-holder").append('<div class="step-answer temp-row-'+$newOrder+'"><label><input type="checkbox" data-catrow="answer-row-cat-'+$answer_cat_id+'" value="1" class="selectoption" onclick="toggleOptionActive($(this))" /><span>'+$answer+'</span></label></div>');
        updatePreview();

        $("#answer-list-table-ec").find(".no-answers").hide();


         $("#step_answer").val("").focus();
    }

}

function updatePreview(){
    //render headline
    $(".phone-preview .headline").html($("#step_title").val());

    //render answers

    if($("#step_subheading").length>0){
        $(".phone-preview .sub-headline").html($("#step_subheading").val()).css({
            "margin-bottom" : "15px",
            "font-size" : "14px"
        });
    }


     if(arguments[0]=="steps_sentence" || arguments[0]=="steps_keywords"){
        $tableObj = $("#answer-list-table tbody").find("tr:not('.no-answers')");
        $tableObj.each(function(){
            $rawClass = $(this).attr("class");
            $txt = $.trim($(this).find(".td-answers").text());
            $(".phone-preview").find(".options-holder").append('<div class="step-answer '+$rawClass+'"><label><input type="checkbox" value="1" class="selectoption" onclick="toggleOptionActive($(this))" /><span>'+$txt+'</span></label></div>')
        })
     }else if(arguments[0]=="steps_expand_collapse"){
        $tableObj = $("#answer-cat-list-table tbody").find("tr:not('.no-answers')");
        $tableObj.each(function(){
            $rawClass = $(this).attr("class");
            $rawClass = $.trim($rawClass);

            $catid = $rawClass.replace("answer-row-cat-","");
            $txt = $.trim($(this).find(".td-answers-cat").text());
            $select = $("#answer-list-table-ec").find("tr[data-catid='"+$rawClass+"'] td").find("table").find("tbody").find("tr");
            $options = "";
            $select.each(function(){
                $aCls = $(this).attr("class");
                $txtanswers = $(this).find(".td-answers span").text();
                $options +="<div class='step-answer "+$aCls+"'><label><input data-catrow='"+$rawClass+"' type='checkbox' value='1' class='selectoption' onclick='toggleOptionActive($(this))' /><span>"+$txtanswers+"</span></label></div>";
            });

            $(".phone-preview").find("#accordion").append('<h4 class="accordion-toggle '+$rawClass+'" onclick="toggleAccordion($(this))">'+$txt+'</h4>'+
                                  '<div class="accordion-content">'+
                                    '<div class="options-holder keywords">'+
                                        '<div class="steps-answer-holder">'+$options+
                                        '</div>'+
                                        '<div data-catrow="'+$rawClass+'" data-catid="'+$catid+'" class="step-answer add-another-button hide frt">'+
                                            '<label><img src="../../images/admin_icons/plus_orange.png" width="18" / >'+$jsLang["add_answer"]+'</label>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="clear"></div>'+
                                  '</div>');
        });

     }

     if($("#button_text").length>0){
        if($.trim($("#button_text").val())!=""){
            $(".text-btn").html($("#button_text").val());
        }


     }

     if($("#answer_text").length>0){
        $(".text-holder").html($("#answer_text").val());
     }



     //show hide add new button new-option
     if($("#allow_to_add_answers").length>0){
         if($("#allow_to_add_answers").prop("checked")===false){
            $(".phone-preview").find(".add-another-button").addClass("hide");
         }else{
            $(".phone-preview").find(".add-another-button").removeClass("hide");
         }
     }

     if($("#allow_to_add_answer_category").length>0){
         if($("#allow_to_add_answer_category").prop("checked")===false){
            $(".phone-preview").find(".add-another-button-cat").addClass("hide");
         }else{
            $(".phone-preview").find(".add-another-button-cat").removeClass("hide");
         }
     }

     if($("#allow_to_edit_list").length>0){
         if($("#allow_to_edit_list").prop("checked")===true){
            $(".steps-counter .edit").removeClass("hide");
         }else{
             $(".steps-counter .edit").addClass("hide");
         }
     }

     if($("#show_date").length>0 && arguments[0]!="steps_date"){
         if($("#show_date").prop("checked")===false){
            $(".phone-preview").find(".date-holder").addClass("hide");
         }else{
            $(".phone-preview").find(".date-holder").removeClass("hide");
         }
     }

     if($("#show_time").length>0){
         if($("#show_time").prop("checked")===false){
            $(".phone-preview").find(".time-holder").addClass("hide");
         }else{
            $(".phone-preview").find(".time-holder").removeClass("hide");
         }
     }

}

function toggleOptionActive(obj){

    var attr = obj.attr('data-catrow');


    if (typeof attr !== typeof undefined && attr !== false) {
         $checkedLength = $(".selectoption[data-catrow='"+attr+"']:checked").length;
        // alert($checkedLength);
    }else{
         $checkedLength = $(".selectoption:checked").length;
    }
    $is_multiple_choice = $("input[name='is_multiple_choice']:checked").val();
    if($is_multiple_choice>0){
        $no_of_allowed_options = $("#max_selection_allowed").val();
        if($checkedLength>$no_of_allowed_options){
            event.preventDefault();
            return false;
        }
    }else{
        if($checkedLength>1){
            event.preventDefault();
            return false;
        }
    }

    if(obj.prop("checked")===true){
        obj.parent("label").parent(".step-answer").addClass("active");
    }else{
        obj.parent("label").parent(".step-answer").removeClass("active");
    }
}

function removeAnswerRow(obj){
    var deleteAnswer = false;

    if(confirm("Are you sure you want to delete this answer?")){
        if(obj.next("a").length==0){
            if(confirm("The answer is in use, is it ok to delete?")){
                deleteAnswer = true;
            }else{
                deleteAnswer = false;
            }
        }else{
            deleteAnswer = true;
        }
    }

    if(deleteAnswer==true){
            $cls = obj.attr("data-rowid");

            //if user tried to delete just added answer, then simply remove the row because its yet not saved in the database
            if($cls.indexOf("temp-row")>=0){ //it is newly added unsaved row, so just remove it.
                $("."+$cls).fadeOut("slow", function(){
                    $(this).remove();
                });
            }

            var dowhat = obj.attr("data-dowhat");

            if(dowhat==1){
                $dowhat = 0;
            } else{
                $dowhat = 1;
            }

            $answer_id = $("."+$cls).find("input[name='answer_id[]']").val();
            if($answer_id>0){ //needed only for deleting row that comes from database

                $.ajax({
                    url: $sitePath + "/minapp/admin/removeStepAnswer",
                    type: "post",
                    data: "answer_id="+$answer_id+"&dowhat="+$dowhat,
                    beforeSend: function(){
                        $("#preLoader").show();
                    },
                    success: function(data){
                        if($.trim(data)!=="success"){
                            return false;
                        }
                    }
                });

            }

           if($dowhat==0){
                $img = "<img src='../../images/admin_icons/enabled.gif'/>";
                $newdowhat = 0;
                $("."+$cls).fadeOut("slow", function(){
                    $(this).remove();
                });
           }else{
                $newdowhat = 1;
                $img = "<img src='../../images/admin_icons/delete.png'/>";
                $("."+$cls).removeClass("red-row");
           }


           obj.attr("data-dowhat",$newdowhat);
           obj.html($img);

            $tableObj = $("#answer-list-table").find("tbody").find("tr").not(".no-answers").length;
           if($tableObj==0){
                $(".no-answers").show();
           }
            updatePreview();
    }
}

function removeAnswerRowEc(obj){
    var deleteRow = false;

    if(confirm("Are you sure you want to delete the answer?")){
        if(obj.next("a").length==0){
            if(confirm("The answer is in use, is it ok to delete?")){
                deleteRow = true;
            }else{
                deleteRow = false;
            }
        }else{
            deleteRow = true;
        }
    }


    if(deleteRow==true){

            $cls = obj.attr("data-rowid");

            var dowhat = obj.attr("data-dowhat");

            if(dowhat==1){
                $dowhat = 0;
            } else{
                $dowhat = 1;
            }

            $answer_id = $("."+$cls).find("input[name='answer_id[]']").val();
            if($answer_id>0){ //needed only for deleting row that comes from database

                $.ajax({
                    url: $sitePath + "/minapp/admin/removeStepAnswer",
                    type: "post",
                    data: "answer_id="+$answer_id+"&dowhat="+$dowhat,
                    beforeSend: function(){
                        $("#preLoader").show();
                    },
                    success: function(data){
                        if($.trim(data)!=="success"){
                            return false;
                        }
                    }
                });
            }



            if($dowhat==0){
                $img = "<img src='../../images/admin_icons/enabled.gif'/>";
                $newdowhat = 0;
                $("."+$cls).fadeOut("slow",function(){
                     $sel = $("tr."+$cls).parent("tbody");
                    $(this).remove();
                     //If all answers for category has been removed then remove category heading as well in answer table.


                    if($sel.find("tr").length==0){

                        $sel.parent("table").closest("tr").remove();
                    }
                });
           }else{
                $newdowhat = 1;
                $img = "<img src='../../images/admin_icons/delete.png'/>";
                $("."+$cls).removeClass("red-row");
           }



            obj.attr("data-dowhat",$newdowhat);
            obj.html($img);



            $tableObj = $("#answer-list-table-ec").find("tbody.main").find("tr").not(".no-answers").length;
           if($tableObj==0){
                $(".no-answers").show();
           }
            updatePreview();
    }
}

function editAnswerRow(obj){
    $cls = obj.attr("data-rowid");
    obj.addClass("hide");
    $td = $("."+$cls).find(".td-answers");
    $answer = $.trim($td.find("span").text());
    $td.html("<div class='edit-"+$cls+"'><input type='text' value='"+$answer+"' class='inputs w85'/>&nbsp;&nbsp;<a href='javascript:void(0)' data-rowid='"+$cls+"' onclick='updateAnswer($(this))'><img src='../../images/admin_icons/tick.gif'/></a></div>")
}

function editAnswerRowCat(obj){
    $cls = obj.attr("data-rowid");
    obj.addClass("hide");
    $td = $("."+$cls).find(".td-answers-cat");
    $answer = $.trim($td.find("span").text());
    $td.html("<div class='edit-"+$cls+"'><input type='text' value='"+$answer+"' class='inputs w85'/>&nbsp;&nbsp;<a href='javascript:void(0)' data-rowid='"+$cls+"' onclick='updateAnswerCat($(this))'><img src='../../images/admin_icons/tick.gif'/></a></div>")
}

function updateAnswer(obj){
    $cls = obj.attr("data-rowid");
    $td = $("."+$cls).find(".td-answers");
    $div = $td.find(".edit-"+$cls);
    $newanswer = $div.find("input[type='text']").val();
    $td.html("<input type='hidden' name='answers[]' value='"+$newanswer+"' /><span>"+$newanswer+"</span>");
    $atag = $td.next("td").find(".edit-row").removeClass("hide");
    $div.remove();
    $(".phone-preview .options-holder").find("."+$cls).find("label span").html($newanswer);
}

function updateAnswerCat(obj){
    $cls = obj.attr("data-rowid");
    $td = $("."+$cls).find(".td-answers-cat");
    $div = $td.find(".edit-"+$cls);
    $newanswer = $div.find("input[type='text']").val();
    $td.html("<input type='hidden' name='answers_category[]' value='"+$newanswer+"' /><span>"+$newanswer+"</span>");
    $atag = $td.next("td").find(".edit-row-cat").removeClass("hide");
    $div.remove();
    $(".phone-preview h4."+$cls).html($newanswer);
}

function removeAnswerRowCat(obj){
    if(confirm("Are you sure you want to delete answer category?")){
            $cls = obj.attr("data-rowid");

            var dowhat = obj.attr("data-dowhat");

            if(dowhat==1){
                $dowhat = 0;
            } else{
                $dowhat = 1;
            }

            $cat_id = $("."+$cls).find("input[name='cat_id[]']").val();
            if($cat_id>0){ //needed only for deleting row that comes from database

                $.ajax({
                    url: $sitePath + "/minapp/admin/removeStepAnswerCat",
                    type: "post",
                    data: "cat_id="+$cat_id+"&dowhat="+$dowhat,
                    beforeSend: function(){
                        $("#preLoader").show();
                    },
                    success: function(data){
                        $("#preLoader").hide();
                        if($.trim(data)!=="success"){
                            return false;
                        }
                    }
                });

            }

            if($dowhat==0){
                $img = "<img src='../../images/admin_icons/enabled.gif'/>";
                $newdowhat = 0; 
                $("."+$cls).fadeOut("slow",function(){ // remove category from the list
                    $(this).remove();
                });
                 $("#answer-list-table-ec").find("tr[data-catid='"+$cls+"']").fadeOut("slow",function(){ //remove answers related to removed category from the list
                        $(this).remove();
                 });
           }else{
                $newdowhat = 1;
                $img = "<img src='../../images/admin_icons/delete.png'/>";
                $("."+$cls).removeClass("red-row-cat");
                 $("#answer-list-table-ec").find("tr[data-catid='"+$cls+"']").removeClass("red-row");
           }


            $("h4."+$cls).next().remove(); //remove Add answers button for the category
            obj.attr("data-dowhat",$newdowhat); //probably not needed this line
            obj.html($img); //probably not needed this line


            if($("#answer-list-table-ec").find("tbody tr:not('.no-answers')").length==0){
                $("#answer-list-table-ec").find(".no-answers").show();
            }

            $("#answer_cat_selector option[value='"+$cat_id+"']").remove(); //remove the category from the select box from answer adding section

            $tableObj = $("#answer-list-table-cat").find("tbody").find("tr").not(".no-answers").length;
           if($tableObj==0){
                $("#answer-list-table-cat").find(".no-answers").show();
           }
            updatePreview();
    }
}


function toggleAccordion(obj){
    obj.next().slideToggle('fast',function(){
        if(obj.next(".accordion-content").is(":visible")){
            obj.addClass("active");
        }else{
            obj.removeClass("active");
        }
    });
    $("#accordion").find("h4").removeClass("active");

    //Hide the other panels
    $(".accordion-content").not(obj.next()).slideUp('fast');
}

function MakeAnswerTableSortable(){
    $('table.ec_grid tbody').sortable({
        opacity: 0.6,
        cursor: 'move',
        scrollSensitivity: 40,
        axis: 'y',
        handle: ".handle",
        helper: function(e,tr){
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index)
            {
              $(this).width($originals.eq(index).width())
            });
            return $helper;
        },
        update: function (event, ui) {
           $(this).find("tr[data-row='row-answer']").each(function(index){ //renumbering the column
                $(this).children("td:first").html("<img src='../../images/admin_icons/reorder.png' width='18' / >");
                $(this).children("td:last").find("input[name='answer_order[]']").val(index+1);
            });
        }
    })
}


function showMappingInterface(){
    hideBox();
    $("#regfilter").hide();
    $path = $sitePath + "/minapp/admin/showMappingInterface";
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        url: $path,
        type: "post",
        data: "",
        beforeSend: function(){

        },
        success: function(data){
            $('#box-registrationtask').html(data);
            $("#frmMapCustomAnswers").validate({
                    rules: {
                        standard_answers: {
                            required: true
                        },
                        search_keywords : {
                            required: true
                        }
                    },
                    messages: {
                        standard_answers: {
                            required: $jsLang['required']
                        },
                        search_keywords : {
                            required: $jsLang['required']
                        }
                    }
                });
        }
    });
}


function getCustomAnswersToMap() {
    //showBox();
    var search_keywords = $("#search_keywords").val();
    var optionToMap = $("#standard_answers").find("option:selected").text();
    var mapWhat = $("#map_what").val();
    var map_id = $("#standard_answers").val();
    var criteria = $("#search_criteria").val();
    var offset = 0;
    var orderBy = 'desc';

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/getCustomAnswersToMap",
        data: {
            offset: offset,
            orderBy: orderBy,
            map_what: mapWhat,
            map_answer_id: map_id,
            search_criteria: criteria,
            option_to_map: optionToMap,
            keywords: search_keywords
        },
        beforeSend: function(){
             if (!$("#frmMapCustomAnswers").valid()) {
                $("#preLoader").hide();
                return false;
            }
        },
        success: function(response) {
            $('#list-of-custom-answers').removeClass("hide").html(response);
        }
    });
}

function getCustomAnswersToMapPaginated(orderBy, offset) {
        if (orderBy == undefined) {
            orderBy = 'desc';
        }
        if (offset == undefined) {
            offset = 0;
        }

        var postData = {};
        postData.offset = offset;
        postData.orderBy = orderBy;
        postData.option_to_map = $("#standard_answers").find("option:selected").text();
        postData.keywords = $("#search_keywords").val();

        $.ajax({
            type: 'post',
            url: $sitePath + "/minapp/admin/getCustomAnswersToMap",
            data: postData,
            success: function(response) {
                $('#list-of-custom-answers').html(response);
            }
        });
    }
//updateAnswer
//
//ADDED BY SABIN on 21st June 2015 >>

function appBackendSelector(){
   hidRegStuffs();
    $.ajax({
        type: "post",
        url: $sitePath + "/minapp/admin/appBackendSelector",
        success: function(response){
            $('#box-appselector').html(response).show();
        }
    });
}

function hidRegStuffs(){
    $("#selhide").hide();
    $("#regfilter").hide();
    $("#box1 .content").hide();
}


function listMyHomework() {
    //showBox();
    $("#map-custom-answers").hide();
    $("#manage-special-answers").hide();
    $("#selhide").hide();
    $("#regfilter").show();
    var diffId = $('#selhide #selTreatment1 :selected').val();
    var filterId = ($("#selhide #selProblem1 :selected").val() != 0) ? $('#selhide #selProblem1 :selected').val() : diffId;
    var filterType = ($("#selhide #selProblem1 :selected").val() != 0) ? 'problem' : 'treatment';
    var offset = 0;
    var orderBy = 'desc';

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/listAllMyhomeworks",
        data: {
            offset: offset,
            orderBy: orderBy,
            diffId: diffId,
            filterId: filterId,
            filterType: filterType
        },
        success: function(response) {
            destroyTinyMCE();
            $('#box-myhomework').html(response);
        }
    });
}

/**
 * Function to show homework form
 */
function addMyHomeworkForm() {
    $("#regfilter").hide();
    $("#myhomework").attr("disabled",true);
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/addMyHomeworkForm",
        data: {},
        beforeSend: function(){

        },
        success: function(response) {

            $('#box-myhomework').html(response).show();
            $("#hw_difficulty_id").dropdownchecklist( { emptyText: "<i>Select Difficulty</i>", width: 300 } );
            $("#frmAddMyHomework").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    headline: {
                        required: true
                    },
                    homework_content: {
                        required: true
                    }
                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    headline: {
                        required: $jsLang['required']
                    },
                    homework_content: {
                        required: $jsLang['required']
                    }
                }
            });

            $("#myhomework").removeAttr("disabled");
        }
    });
}

function listPaginatedHomeWorks(orderBy, offset) {
        if (orderBy == undefined) {
            orderBy = 'desc';
        }
        if (offset == undefined) {
            offset = 0;
        }

        var postData = {};
        postData.offset = offset;
        postData.orderBy = orderBy;
        postData.diffId = $('#diffId').val();
        postData.filterId = $('#filterId').val();
        postData.filterType = $('#filterType').val();
        console.log(postData);

        $.ajax({
            type: 'post',
            url: $sitePath + "/minapp/admin/listAllHomeworksAjax",
            data: postData,
            success: function(response) {
                $('#box-myhomework').html(response);
            }
        });
    }

function toggleRegHomeworkstatus(obj,newStatus){
     var deleteHomework = false;
    if(confirm("Are you sure you want to delete this item?")){
        if(obj.attr("data-inuse")==1){
            if(confirm("This item is already in use. Do you want to delete this?")){
                deleteHomework = true;
            }
        }else{
            deleteHomework = true;
        }

        if(deleteHomework==true){
                var hwID = obj.data("hwid");
                $.ajax({
                    url: $sitePath + "/minapp/admin/changeHomeworkStatus",
                    type: "POST",
                    async:false,
                    data: "homework_id="+hwID+"&new_status="+newStatus,
                    beforeSend:function(){
                         $("#preLoader").show();
                    },
                    success:function(data){
                        $("#preLoader").hide();
                        obj.closest("tr").fadeOut('slow',function(){
                            $(this).remove();
                        });
                        /*$status = newStatus==0?1:0;
                        obj.attr("onclick","toggleRegHomeworkstatus($(this),"+$status+")");

                        var d = $.parseJSON(data);
                        obj.find("img").attr("src",decodeURIComponent(d.icon_path));
                        obj.attr("title",d.tooltip);*/
                    }
                });
        }
    }

}

function editMyHomework(homework_id) {
    hideBox();
     $("#regfilter").hide();
    $path = $sitePath + "/minapp/admin/editMyHomework";
    $(document)[0].title = 'BIP Admin Panel';

    $.ajax({
        type: 'post',
        url: $path,
        data: {
            homework_id: homework_id
        },
        success: function(response) {
            $('#box-myhomework').html(response);
            $("#hw_difficulty_id").dropdownchecklist( { emptyText: "<i>Select Difficulty</i>", width: 300 } );
            $("#frmAddMyHomework").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    headline: {
                        required: true
                    },
                    homework_content: {
                        required: true
                    }
                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    headline: {
                        required: $jsLang['required']
                    },
                    homework_content: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}

function saveMyHomework() {
    hideBox();
    $(document)[0].title = 'BIP Admin Panel';
    var str = $("#frmAddMyHomework").serialize();
    $.ajax({
        type: "Post",
        url: $sitePath + "/minapp/admin/saveMyHomework",
        data: str,
        async: false,
        beforeSend: function() {
            var content= tinyMCE.get('homework_content').getContent();
            $("#homework_content").val(content);

            if (!$("#frmAddMyHomework").valid()) {
                $("#preLoader").hide();
                return false;
            }
        },
        success: function(response) {
            $r = $.parseJSON(response);
            if($r.error_code=="duplicate" || $r.error_code=="error"){
                alert($r.error_msg);
                return false;
            }else{
                $("#selTreatment1").val(getCookie("bip_default_difficulty"));
                listMyHomework();
            }
        },
        error: function() {
            console.log("error");
        }

    });
}


function destroyTinyMCE(){
    if(typeof tinyMCE!="undefined") tinyMCE.remove();
}




function listMyCrisisplan() {

    //showBox();
    $("#map-custom-answers").hide();
    $("#manage-special-answers").hide();
    $("#selhide").hide();
    $("#regfilter").show();
    var diffId = $('#selhide #selTreatment1 :selected').val();
    var filterId = ($("#selhide #selProblem1 :selected").val() != 0) ? $('#selhide #selProblem1 :selected').val() : diffId;
    var filterType = ($("#selhide #selProblem1 :selected").val() != 0) ? 'problem' : 'treatment';
    var offset = 0;
    var orderBy = 'desc';

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/listAllMycrisisplans",
        data: {
            offset: offset,
            orderBy: orderBy,
            diffId: diffId,
            filterId: filterId,
            filterType: filterType
        },
        success: function(response) {
            destroyTinyMCE();
            $('#box-crisisplan').html(response);
        }
    });
}

/**
 * Function to show crisis plan form
 */
function addMyCrisisplanForm() {
    $("#regfilter").hide();
    $("#crisisplan").attr("disabled",true);
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/addMyCrisisplanForm",
        data: {},
        success: function(response) {
            $('#box-crisisplan').html(response).show();
            $("#cp_difficulty_id").dropdownchecklist( { emptyText: "<i>Select Difficulty</i>", width: 300 } );
            $("#frmAddMyCrisisplan").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    headline: {
                        required: true
                    },
                    plan_content: {
                        required: true
                    }
                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    headline: {
                        required: $jsLang['required']
                    },
                    plan_content: {
                        required: $jsLang['required']
                    }
                }
            });

             $("#crisisplan").removeAttr("disabled");
        }
    });
}

function listPaginatedCrisisplans(orderBy, offset) {
        if (orderBy == undefined) {
            orderBy = 'desc';
        }
        if (offset == undefined) {
            offset = 0;
        }

        var postData = {};
        postData.offset = offset;
        postData.orderBy = orderBy;
        postData.diffId = $('#diffId').val();
        postData.filterId = $('#filterId').val();
        postData.filterType = $('#filterType').val();
        console.log(postData);

        $.ajax({
            type: 'post',
            url: $sitePath + "/minapp/admin/listAllCrisisplansAjax",
            data: postData,
            success: function(response) {
                $('#box-crisisplan').html(response);
            }
        });
    }

function toggleRegCrisisplanstatus(obj,newStatus){
    if(confirm("Are you sure you want to delete selected plan?")){
        var hwID = obj.data("hwid");
        $.ajax({
            url: $sitePath + "/minapp/admin/changeCrisisplanStatus",
            type: "POST",
            async:false,
            data: "plan_id="+hwID+"&new_status="+newStatus,
            beforeSend:function(){
                 $("#preLoader").show();
            },
            success:function(data){
                $("#preLoader").hide();
                obj.closest("tr").fadeOut("slow",function(){
                        $(this).remove();
                });
              /*  $status = newStatus==0?1:0;
                obj.attr("onclick","toggleRegCrisisplanstatus($(this),"+$status+")");

                var d = $.parseJSON(data);
                obj.find("img").attr("src",decodeURIComponent(d.icon_path));
                obj.attr("title",d.tooltip);*/
            }
        });
    }
}

function editMyCrisisplan(plan_id) {
    hideBox();
     $("#regfilter").hide();
    $path = $sitePath + "/minapp/admin/editMyCrisisplan";
    $(document)[0].title = 'BIP Admin Panel';

    $.ajax({
        type: 'post',
        url: $path,
        data: {
            plan_id: plan_id
        },
        success: function(response) {
            $('#box-crisisplan').html(response);
            $("#cp_difficulty_id").dropdownchecklist( { emptyText: "<i>Select Difficulty</i>", width: 300 } );
            $("#frmAddMyCrisisplan").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    headline: {
                        required: true
                    },
                    plan_content: {
                        required: true
                    }
                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    headline: {
                        required: $jsLang['required']
                    },
                    plan_content: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

function saveMyCrisisplan() {
    hideBox();
    $(document)[0].title = 'BIP Admin Panel';
    var str = $("#frmAddMyCrisisplan").serialize();
    $.ajax({
        type: "Post",
        url: $sitePath + "/minapp/admin/saveMyCrisisplan",
        data: str,
        async: false,
        beforeSend: function() {
            var content= tinyMCE.get('plan_content').getContent();
            $("#plan_content").val(content);

            if (!$("#frmAddMyCrisisplan").valid()) {
                $("#preLoader").hide();
                return false;
            }
        },
        success: function(response) {
            $r = $.parseJSON(response);
            if($r.error_code=="duplicate" || $r.error_code=="error"){
                alert($r.error_msg);
                return false;
            }else{
                $("#selTreatment1").val(getCookie("bip_default_difficulty"));
                listMyCrisisplan();
            }
        },
        error: function() {
            console.log("error");
        }

    });
}
//ADDED BY SABIN on 21st June 2015 <<

//ADDED BY SABIN on 2nd July 2015 >>
function listMySkillsModule() {
    //showBox();
    $("#selhide").hide();
    $("#regfilter").hide();

    if(arguments.length>0){
        var diffId = arguments[0];
        var filterId = arguments[0];
    }else{
        var diffId = $('#filter-difficulty :selected').val();
        var filterId = $('#filter-difficulty :selected').val();
    }

    var filterType = "treatement";

    var offset = 0;
    var orderBy = 'desc';

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/listAllMySkillsModule",
        data: {
            offset: offset,
            orderBy: orderBy,
            diffId: diffId,
            filterId: filterId,
            filterType: filterType
        },
        success: function(response) {
            $('#box-myskills').html(response);
        }
    });
}

function listPaginatedModules(orderBy, offset) {
        if (orderBy == undefined) {
            orderBy = 'desc';
        }
        if (offset == undefined) {
            offset = 0;
        }

        var postData = {};
        postData.offset = offset;
        postData.orderBy = orderBy;
        postData.diffId = $('#frmModuleList').find("#diffId").val();
        postData.filterId = $('#frmModuleList').find("#filterId").val();
        postData.filterType = $('#frmModuleList').find('#filterType').val();

        $.ajax({
            type: 'post',
            url: $sitePath + "/minapp/admin/listAllModulesAjax",
            data: postData,
            success: function(response) {
                $('#box-myskills').html(response);
            }
        });
}


function toggleSkillModulestatus(obj,newStatus){
    var hwID = obj.data("hwid");
     var deleteModule = false;
    if(confirm("Are you sure you want to delete this module?")){
        if(obj.attr("data-hasskills")==1){
            if(confirm("The module is already in use. Do you want to delete this?")){
                deleteModule = true;
            }
        }else{
            deleteModule = true;
        }

        if(deleteModule==true){
                $.ajax({
                    url: $sitePath + "/minapp/admin/changeSkillModulesStatus",
                    type: "POST",
                    async:false,
                    data: "module_id="+hwID+"&new_status="+newStatus,
                    beforeSend:function(){
                         $("#preLoader").show();
                    },
                    success:function(data){
                        $("#preLoader").hide();
                      /*  $status = newStatus==0?1:0;
                        obj.attr("onclick","toggleSkillModulestatus($(this),"+$status+")");

                        var d = $.parseJSON(data);
                        obj.find("img").attr("src",decodeURIComponent(d.icon_path));
                        obj.attr("title",d.tooltip);*/
                         obj.parent("td").parent("tr").fadeOut("slow", function(){
                                $(this).remove();
                        })
                    }
                });
        }
    }
}

function editMySkillModule(module_id) {
    hideBox();
     $("#regfilter").hide();
    $path = $sitePath + "/minapp/admin/editMySkillModule";
    $(document)[0].title = 'BIP Admin Panel';

    $.ajax({
        type: 'post',
        url: $path,
        data: {
            module_id: module_id
        },
        success: function(response) {
            $('#box-myskills').html(response);
            $("#cp_difficulty_id").dropdownchecklist( { emptyText: "<i>Select Difficulty</i>", width: 300 } );
            $("#frmAddSkillModules").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    module_name: {
                        required: true
                    }
                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    module_name: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}

function saveMySkillsModule() {
   var args = arguments;

    hideBox();
    $(document)[0].title = 'BIP Admin Panel';
    var str = $("#frmAddSkillModules").serialize();
    $.ajax({
        type: "Post",
        url: $sitePath + "/minapp/admin/saveMySkillsModule",
        data: str,
        async: false,
        beforeSend: function() {

            if (!$("#frmAddSkillModules").valid()) {
                $("#preLoader").hide();
                return false;
            }

            if(args.length>0){
                args[0].attr("disabled","disabled");
            }
        },
        success: function(response) {
            $r = $.parseJSON(response);
            if($r.error_code=="duplicate" || $r.error_code=="error"){
                alert($r.error_msg);
                $('#frmAddSkillModules input[name=btnSave]').attr("disabled",false);
                return false;
            }else{
                listMySkillsModule();
            }
        },
        error: function() {
            console.log("error");
        }

    });
}


function addSkillModulesForm() {
    $("#regfilter").hide();
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/addSkillModulesForm",
        data: {
            selected_difficulty: $("#filter-difficulty").val()
        },
        success: function(response) {
            $('#box-myskills').html(response).show();
            $("#cp_difficulty_id").dropdownchecklist( { emptyText: "<i>Select Difficulty</i>", width: 300 } );
            $("#frmAddSkillModules").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    module_name: {
                        required: true
                    }
                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    module_name: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}

function listPaginatedSkills(orderBy, offset) {
        if (orderBy == undefined) {
            orderBy = 'desc';
        }
        if (offset == undefined) {
            offset = 0;
        }

        var postData = {};
        postData.offset = offset;
        postData.orderBy = orderBy;
        postData.moduleId = $('#moduleId').val();

        $.ajax({
            type: 'post',
            url: $sitePath + "/minapp/admin/listAllSkillsAjax",
            data: postData,
            success: function(response) {
                $('#box-myskills').html(response);
            }
        });
}


function toggleSkillstatus(obj,newStatus){
    var deleteSkill = false;
    if(confirm("Are you sure you want to delete this item?")){
        if(obj.attr("data-inuse")==1){
            if(confirm("This item is already in use. Do you want to delete this?")){
                deleteSkill = true;
            }
        }else{
            deleteSkill = true;
        }

        if(deleteSkill==true){
            var hwID = obj.data("hwid");
            $.ajax({
                url: $sitePath + "/minapp/admin/changeSkillStatus",
                type: "POST",
                async:false,
                data: "skill_id="+hwID+"&new_status="+newStatus,
                beforeSend:function(){
                     $("#preLoader").show();
                },
                success:function(data){
                    $("#preLoader").hide();
                    /*$status = newStatus==0?1:0;
                    obj.attr("onclick","toggleSkillstatus($(this),"+$status+")");

                    var d = $.parseJSON(data);
                    obj.find("img").attr("src",decodeURIComponent(d.icon_path));
                    obj.attr("title",d.tooltip);*/
                    if(newStatus==0){
                        obj.parent("td").parent("tr").fadeOut("slow", function(){
                                $(this).remove();
                        })
                    }
                }
            });
        }
    }
}

function listMySkills(module_id){
    $.ajax({
            url: $sitePath+"/minapp/admin/listMySkills",
            data: "module_id="+module_id,
            type: "Post",
            beforeSend: function(){
                $("#preLoader").hide();
            },
            success: function(response){
                $("#box-myskills").html(response);
            }
        });
}

function editMySkill(skillId, moduleId){
    $.ajax({
        url: $sitePath + "/minapp/admin/editMySkill",
        data: "module_id="+moduleId+"&skill_id="+skillId,
        type: "post",
        beforeSend: function(){
            $("#preLoader").hide();
        },
        success: function(response){
            destroyTinyMCE();
            $("#box-myskills").html(response);
        }
    });
}

function listFindFeelings(){

    if(arguments.length>0){
        var diffId = arguments[0];
        var filterId = arguments[0];;
        var filterType = 'treatment';
    }else{
        var diffId = $('#selhide #selTreatment1 :selected').val();
        var filterId = ($("#selhide #selProblem1 :selected").val() != 0) ? $('#selhide #selProblem1 :selected').val() : diffId;
        var filterType = ($("#selhide #selProblem1 :selected").val() != 0) ? 'problem' : 'treatment';
    }
    var offset = 0;
    var orderBy = 'desc';

    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/listFindFeelings",
        data: {
            offset: offset,
            orderBy: orderBy,
            diffId: diffId,
            filterId: filterId,
            filterType: filterType
        },
        success: function(response) {
            $('#box-myskills').html(response);
        }
    });
}

function listPaginatedFeelings(orderBy, offset) {
        if (orderBy == undefined) {
            orderBy = 'desc';
        }
        if (offset == undefined) {
            offset = 0;
        }

        var postData = {};
        postData.offset = offset;
        postData.orderBy = orderBy;
        postData.diffId = $('#diffId').val();
        postData.filterId = $('#filterId').val();
        postData.filterType = $('#filterType').val();
        console.log(postData);

        $.ajax({
            type: 'post',
            url: $sitePath + "/minapp/admin/listAllFeelingsAjax",
            data: postData,
            success: function(response) {
                $('#box-myskills').html(response);
            }
        });
}


function toggleMyFeelingStatus(obj,newStatus){
    var deleteFeelings = false;
    if(confirm("Are you sure you want to delete this item?")){
        if(obj.attr("data-inuse")==1){
            if(confirm("This item is already in use. Do you want to delete this?")){
                deleteFeelings = true;
            }
        }else{
            deleteFeelings = true;
        }

        if(deleteFeelings==true){
                var hwID = obj.data("hwid");
                $.ajax({
                    url: $sitePath + "/minapp/admin/changeMyFeelingStatus",
                    type: "POST",
                    async:false,
                    data: "feeling_id="+hwID+"&new_status="+newStatus,
                    beforeSend:function(){
                         $("#preLoader").show();
                    },
                    success:function(data){
                        $("#preLoader").hide();

                        obj.parent("td").parent("tr").fadeOut("slow", function(){
                            $(this).remove();
                            reindexTheRowNumber(".feelings-row");
                        })
                        /*$status = newStatus==0?1:0;
                        obj.attr("onclick","toggleMyFeelingStatus($(this),"+$status+")");

                        var d = $.parseJSON(data);
                        obj.find("img").attr("src",decodeURIComponent(d.icon_path));
                        obj.attr("title",d.tooltip);*/
                    }
                });
        }
    }
}

function reindexTheRowNumber(selector){
    $(selector).each(function(index){ //renumbering the column
        $(this).children("td:first").html((index+1)+"<img src='../../images/admin_icons/reorder.png' width='18' style='margin-left:5px' / >");
        if(index%2==1){
            $class = "odd";
        }else{
            $class = "even";
        }
        $(this).removeClass("odd").removeClass("even").addClass($class);
    });
}


function addNewFeelingForm() {
   // $("#regfilter").hide();
    $(document)[0].title = 'BIP Admin Panel';
    $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/admin/addNewFeelingForm",
        data: {
            selected_difficulty: $("#sel_difficulty_feelings").val()
        },
        success: function(response) {
            destroyTinyMCE();
            $('#box-myskills').html(response).show();
            $("#cp_difficulty_id").dropdownchecklist( { emptyText: "<i>Select Difficulty</i>", width: 300 } );
            $("#frmAddMyFeelings").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    feeling_name: {
                        required: true
                    },
                    description: {
                        required: true
                    }
                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    feeling_name: {
                        required: $jsLang['required']
                    },
                    description: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}


function saveMyFindFeelings() {
    hideBox();
    $(document)[0].title = 'BIP Admin Panel';
    var content = tinyMCE.get('description').getContent();
    $("#description").val(content);

    var str = $("#frmAddMyFeelings").serialize();


    $.ajax({
        type: "Post",
        url: $sitePath + "/minapp/admin/saveMyFindFeelings",
        data: str,
        async: false,
        beforeSend: function() {
            if (!$("#frmAddMyFeelings").valid()) {
                $("#preLoader").hide();
                return false;
            }
            $("#frmAddMyFeelings").find("#btnSave").attr("disabled","disabled");
        },
        success: function(response) {
            $r = $.parseJSON(response);
            if($r.error_code=="duplicate" || $r.error_code=='error'){
                alert($r.error_msg);
                $('#frmAddMyFeelings input[name=btnSave]').attr('disabled',false);
                return false;
            }else{
                listFindFeelings();
            }
        },
        error: function() {
            console.log("error");
        }

    });
}

function editMyFindFeeling(feeling_id) {
    hideBox();
     $("#regfilter").hide();
    $path = $sitePath + "/minapp/admin/editMyFindFeeling";
    $(document)[0].title = 'BIP Admin Panel';

    $.ajax({
        type: 'post',
        url: $path,
        data: {
            feeling_id: feeling_id
        },
        success: function(response) {
            destroyTinyMCE();
            $('#box-myskills').html(response);
            $("#cp_difficulty_id").dropdownchecklist( { emptyText: "<i>Select Difficulty</i>", width: 300 } );
            $("#frmAddMyFeelings").validate({
                rules: {
                    'difficulty_id[]': {
                        required: true
                    },
                    feeling_name: {
                        required: true
                    },
                    description: {
                        required: true
                    }
                },
                messages: {
                    'difficulty_id[]': {
                        required: $jsLang['required']
                    },
                    feeling_name: {
                        required: $jsLang['required']
                    },
                    description: {
                        required: $jsLang['required']
                    }
                }
            });
        }
    });
}

$( document ).ajaxStart(function() {
  $( "#preLoader" ).show();
});

$( document ).ajaxStop(function() {
  $( "#preLoader" ).hide();
});
//ADDED BY SABIN on 2nd July 2015 <<

//ADDED BY SABIN on 4th August 2015 >>
function filterFindFeelings(orderBy, offset, filterId, filterType) {
    var diffId = $('#sel_difficulty_feelings :selected').val();
    if (offset == undefined) {
        offset = 0;
    }
    if (orderBy == undefined) {
        orderBy = 'desc';
    }
    var url, element;

    url = $sitePath + "/minapp/admin/listFindFeelings";
    element = "box-myskills";

    $.ajax({
        type: 'post',
        url: url,
        data: {
            offset: offset,
            orderBy: orderBy,
            diffId: diffId,
            filterId: filterId,
            filterType: filterType
        },
        success: function(response) {
            $('#'+element).html(response);
        }
    });
}

function listPaginatedFindFeelings(orderBy, offset) {
        if (orderBy == undefined) {
            orderBy = 'desc';
        }
        if (offset == undefined) {
            offset = 0;
        }

        var postData = {};
        postData.offset = offset;
        postData.orderBy = orderBy;
        postData.diffId = $('#FeelingdiffId').val();
        postData.filterId = $('#FeelingfilterId').val();
        postData.filterType = $('#FeelingfilterType').val();
        console.log(postData);

        $.ajax({
            type: 'post',
            url: $sitePath + "/minapp/admin/listAllFeelingsAjax",
            data: postData,
            success: function(response) {
                $('#box-myskills').html(response);
            }
        });
    }

function updateExposureStepPreview(){
     $alternateTextHeight = $(".pvw-alternate-text").height();
     $(".phone-preview").find(".contents").css("height",(285-$alternateTextHeight)+"px");

    $(".phone-preview .headline").html($("#step_title").val());

    if($("#same_title_as_skill_exposure").length>0 && $("#same_title_as_skill_exposure").prop("checked")){
        $(".phone-preview .headline").html($("#step_title").attr("data-defaultext"));
        $("#step_title").val("Same as Exposure name");
    }



    if($("#step_countdown_title").length>0) $(".phone-preview").find(".cd-headline").html($("#step_countdown_title").val());

    if($("#countdown_desc").length>0)  $(".phone-preview").find(".cd-text").html($("#countdown_desc").val());


    if($("#alternate_text").length>0)  $(".phone-preview .pvw-alternate-text").html($("#alternate_text").val());

    if($("#pvw_label_for_10").length>0)  $("#pvw_label_for_10").html($("#step_label_10").val());
    if($("#pvw_label_for_0").length>0)  $("#pvw_label_for_0").html($("#step_label_0").val());

    if($("#answer_text").length>0)  $(".text-holder").html($("#answer_text").val());

    $noAnswerFloats = arguments[0]=="step_ec_sentences" ? "style='float:none'" : "";
    //Test
     if(arguments[0]=="step_keywords" || arguments[0]=="step_sentences"){
        $tableObj = $("#answer-list-table tbody").find("tr:not('.no-answers')");
        $tableObj.each(function(){
            $rawClass = $(this).attr("class");
            $txt = $.trim($(this).find(".td-answers").text());
            $(".phone-preview").find(".options-holder").append('<div class="step-answer '+$rawClass+'"><label><input type="checkbox" value="1" class="selectoption" onclick="toggleOptionActive($(this))" /><span>'+$txt+'</span></label></div>')
        })
     }else if(arguments[0]=="step_ec_descriptions"){
        $tableObj = $("#answer-cat-list-table tbody").find("tr:not('.no-answers')");
        $tableObj.each(function(){
            $rawClass = $(this).attr("class");
            $rawClass = $.trim($rawClass);
            $catid = $rawClass.replace("answer-row-cat-","");
            $txt = $.trim($(this).find(".td-answers-cat").text());
            $content = $.trim($(this).find(".cat-contents").text());
            $divcontent = "<div class='answer-cat-content'>"+$content+"</div>";
            $greenbutton = "<div class='accept-btn-holder' style='margin-top:10px;margin-bottom:5px;'><a href='javascript:void(null)' class='new-btn' style='width:91%;display:inline-block;text-align:center;'>Den här beskrivningen stämmer</a></div>"


            $(".phone-preview").find("#accordion").append('<h4 class="accordion-toggle '+$rawClass+'" onclick="toggleAccordion($(this))">'+$txt+'</h4>'+
                                  '<div class="accordion-content">'+
                                    '<div class="options-holder keywords">'+
                                        '<div class="steps-answer-holder">'+$divcontent+$greenbutton+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="clear"></div>'+
                                  '</div>');

        });


     }else if(arguments[0]=="step_ec_words" || arguments[0]=="step_ec_sentences"){
        $tableObj = $("#answer-cat-list-table tbody").find("tr:not('.no-answers')");
        $tableObj.each(function(){
            $rawClass = $(this).attr("class");
            $rawClass = $.trim($rawClass);



            $catid = $rawClass.replace("answer-row-cat-","");

            $txt = $.trim($(this).find(".td-answers-cat").text());
            $select = $("#answer-list-table-ec").find("tr[data-catid='"+$rawClass+"'] td").find("table").find("tbody").find("tr");

            $options = "";
             console.warn('$("#answer-list-table-ec").find("tr[data-catid="'+$rawClass+'"] td").find("table").find("tbody").find("tr")');
            $select.each(function(){

                $aCls = $(this).attr("class");
                $txtanswers = $(this).find(".td-answers span").text();
                $options +="<div class='step-answer "+$aCls+"' "+$noAnswerFloats+"><label><input data-catrow='"+$rawClass+"' type='checkbox' value='1' class='selectoption' onclick='toggleOptionActive($(this))' /><span>"+$txtanswers+"</span></label></div>";
            });

            $(".phone-preview").find("#accordion").append('<h4 class="accordion-toggle '+$rawClass+'" onclick="toggleAccordion($(this))">'+$txt+'</h4>'+
                                  '<div class="accordion-content">'+
                                    '<div class="options-holder keywords">'+
                                        '<div class="steps-answer-holder">'+$options+
                                        '</div>'+
                                        '<div data-catrow="'+$rawClass+'" data-catid="'+$catid+'" class="step-answer add-another-button hide frt">'+
                                            '<label><img src="../../images/admin_icons/plus_orange.png" width="18" / >'+$jsLang["add_answer"]+'</label>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="clear"></div>'+
                                  '</div>');
        });

     }

     if($("#button_text").length>0){
        if($.trim($("#button_text").val())!=""){
            $(".text-btn").html($("#button_text").val());
        }


     }

     if($("#answer_text").length>0){
        $(".text-holder").html($("#answer_text").val());
     }



     //show hide add new button new-option
     if($("#allow_to_add_answers").length>0){
         if($("#allow_to_add_answers").prop("checked")===false){
            $(".phone-preview").find(".add-another-button").addClass("hide");
         }else{
            $(".phone-preview").find(".add-another-button").removeClass("hide");
         }
     }

     if($("#allow_to_add_answer_category").length>0){
         if($("#allow_to_add_answer_category").prop("checked")===false){
            $(".phone-preview").find(".add-another-button-cat").addClass("hide");
         }else{
            $(".phone-preview").find(".add-another-button-cat").removeClass("hide");
         }
     }

     if($("#allow_to_edit_list").length>0){
         if($("#allow_to_edit_list").prop("checked")===true){
            $(".steps-counter .edit").removeClass("hide");
         }else{
             $(".steps-counter .edit").addClass("hide");
         }
     }

     if($("#show_date").length>0){
         if($("#show_date").prop("checked")===false){
            $(".phone-preview").find(".date-holder").addClass("hide");
         }else{
            $(".phone-preview").find(".date-holder").removeClass("hide");
         }
     }

     if($("#show_time").length>0){
         if($("#show_time").prop("checked")===false){
            $(".phone-preview").find(".time-holder").addClass("hide");
         }else{
            $(".phone-preview").find(".time-holder").removeClass("hide");
         }
     }



}
//ADDED BY SABIN on 4th August 2015 <<

//ADDED BY SABIN on 12th August 2015 >>
function AddExposureNewAnswerCat(){
    $answer_cat = $.trim($("#step_answer_cat").val());

    //to add answer category one should provide step title so that we can save category to database.
    if($.trim($("#step_title").val())=="" && $("#step_id").val()==0){
        alert($jsLang["specify_step_title"]);
        return false;
    }
    //check if same category already added.
    $test = $.inArray($answer_cat.toLowerCase(),setArrayFromInput("answers_category[]"));
    if($test>-1){
        alert($jsLang["item_in_list"]);
        return false;
    }

    if($.trim($answer_cat)!=""){
        //save step to the database first
        $.ajax({
            url: $sitePath + "/minapp/admin/saveExposureAnswerCategory",
            data: $("#frmAddExposureSteps").serialize(),
            type: "POST",
            beforeSend:function(){ $("#preLoader").show();},
            success: function(data){
                var d = $.parseJSON(data);
                $("#preLoader").hide();
                if(parseInt(d.step_id)>0){
                    $("#step_id").val(d.step_id);
                    if(d.new_step===1){
                         $class = (d.sort_order%2==1)?"even":"odd";
                       /*  $("#step-list-table").find("tbody  tr.no-steps").remove();
                         //$no_data_row.
                         $("#step-list-table").append("<tr id='ID_"+d.step_id+"' class='"+$class+" step-row'>" +
                                                 "<td class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>" +
                                                 "<td class='handle' data-stepid='"+d.step_id+"'>"+$("#step_title").val()+"</td>"+
                                                 "<td class='handle'>"+d.template_name+"</td>" +
                                                "<td><a href='javascript:void(0)' data-stepid='"+d.step_id+"' data-newstatus='0' class='link-green change-step-status' title='Change Status to Inactive'><img src='../../images/admin_icons/enabled.gif'></a></td>"+
                                                "<td><a href='javascript:void(0)' onclick=\"showSelectedStep('"+d.skill_id+"','"+d.flow_id+"','"+d.template+"',"+d.step_id+")\"><img src='../../images/admin_icons/edit.png'></a></td>"
                                                 );*/
                    }


                    $tableObj = $("#answer-cat-list-table").find("tbody");
                    $rowCount = $tableObj.find("tr").not(".no-answers").length;
                    //$newOrder = (d.sort_order>0)?d.sort_order: $rowCount+1;
                    $newOrder = d.cat_id;
                    $html = "<tr data-row='row-answer-cat' class='answer-row-cat-"+$newOrder+"'>";
                    $cls = "answer-row-cat-"+$newOrder;
                    $html +="<td class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>";
                    $html +="<td class='td-answers-cat handle'><input type='hidden' name='answers_category[]' class='inputs w100' value='"+$answer_cat+"' /><span>"+$answer_cat+"</span></td>";
                    $html +="<td><a href='javascript:void(0)' class='remove-row-cat' data-dowhat='1' data-rowid='"+$cls+"' onclick='removeExposureAnswerRowCat($(this))'><img src='../../images/admin_icons/delete.png'/></a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='edit-row-cat' data-rowid='"+$cls+"' onclick='editExposureAnswerRowCat($(this))'><img src='../../images/admin_icons/edit.png'/></a><input type='hidden' name='cat_id[]' value='"+d.cat_id+"' /><input type='hidden' name='answer_cat_order[]' value='"+d.cat_sort_order+"' /></td>";
                    $html +="</tr>";

                    $tableObj.append($html);

                    $(".phone-preview").find("#accordion").append('<h4 class="accordion-toggle active answer-row-cat-'+$newOrder+'"  onclick="toggleAccordion($(this))">'+$answer_cat+'</h4>'+
                              '<div class="accordion-content">'+
                                '<div class="options-holder keywords">'+
                                    '<div class="steps-answer-holder">'+
                                    '</div>'+
                                    '<div data-catid="'+d.cat_id+'" data-catrow="'+$cls+'" class="step-answer add-another-button hide frt">'+
                                        '<label><img src="../../images/admin_icons/plus_orange.png" width="18" / >'+$jsLang["add_answer"]+'</label>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="clear"></div>'+
                              '</div>');
                    $acc = $(".answer-row-cat-"+$newOrder);

                    $(".accordion-content").not($acc.next()).slideUp('fast');
                    $(".accordion-toggle").not($cls).removeClass("active");
                    $("h4."+$cls).addClass("active");
                    $acc.next().slideToggle("fast");

                    updateExposureStepPreview();


                    $("#answer-cat-list-table").find(".no-answers").hide();

                    $("#answer_cat_selector").append("<option data-rowid='"+$cls+"' value='"+d.cat_id+"'>"+$answer_cat+"</option>");

                    $("#step_answer_cat").val("").focus();
                }

            }
        });


    }
}



function AddNewExposureAnswerWithCat(){
    $answer_cat_id = $("#answer_cat_selector option:selected").val();
    $answer = $("#step_answer").val();
    $cat_row_id = $("#answer_cat_selector option:selected").attr("data-rowid");

    if($.trim($answer_cat_id)==""){
        alert($jsLang["select_answer_category"]);
        return false;
    }else if($.trim($answer)==""){
        alert($jsLang["specify_answer"]);
        return false;
    }else{

        $checkTableCat = $("#answer-for-catid-"+$answer_cat_id).length;
        if($checkTableCat==0){
            $newOrder=1;
            $cls = "temp-row-"+$answer_cat_id+"-"+$newOrder;
            $html ="<tr data-catid='answer-row-cat-"+$answer_cat_id+"'><td colspan='3'>";
            $html +="<table cellpadding='0' cellspacing='0' border='0' width='100%' class='ec_grid' id='answer-for-catid-"+$answer_cat_id+"'>";
            $html +="<thead><tr><td colspan='3'>"+$("#answer_cat_selector option:selected").text()+"</td></tr></thead>";
            $html +="<tbody>";
            $html += "<tr data-row='row-answer' class='"+$cls+"'>";
            $html +="<td width='15%' class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>";
            $html +="<td width='60%' class='td-answers handle'><input type='hidden' name='answers[]' class='inputs w100' value='"+$answer+"' /><span>"+$answer+"</span></td>";
            $html +="<td width='25%'><a href='javascript:void(0)' class='remove-row' data-dowhat='1' data-rowid='"+$cls+"' onclick='removeExposureAnswerRowEc($(this))'><img src='../../images/admin_icons/delete.png'/></a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='edit-row' data-rowid='"+$cls+"' onclick='editExposureAnswerRow($(this))'><img src='../../images/admin_icons/edit.png'/></a><input type='hidden' name='answer_cat_id[]' value='"+$answer_cat_id+"' /><input type='hidden' name='answer_id[]' value='0' /><input type='hidden' name='answer_order[]' value='"+$newOrder+"' /></td>";
            $html +="</tr>";
            $html +="</tbody>";
            $html +="</table>";
            $html +="</td></tr>";
            $tableObj = $("#answer-list-table-ec").find("tbody.main").append($html);
            MakeAnswerTableSortable();
        }else{
            $tableObj = $("#answer-for-catid-"+$answer_cat_id).find("tbody");
            $rowCount = $tableObj.find("tr").not(".no-answers").length;
            $newOrder = $rowCount+1;
            $cls = "temp-row-"+$answer_cat_id+"-"+$newOrder;
            $html = "<tr data-row='row-answer' class='"+$cls+"'>";
            $html +="<td class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>";
            $html +="<td class='td-answers handle'><input type='hidden' name='answers[]' class='inputs w100' value='"+$answer+"' /><span>"+$answer+"</span></td>";
            $html +="<td><a href='javascript:void(0)' class='remove-row' data-rowid='"+$cls+"' onclick='removeExposureAnswerRowEc($(this))'><img src='../../images/admin_icons/delete.png'/></a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='edit-row' data-rowid='"+$cls+"' onclick='editExposureAnswerRow($(this))'><img src='../../images/admin_icons/edit.png'/></a><input type='hidden' name='answer_cat_id[]' value='"+$answer_cat_id+"' /><input type='hidden' name='answer_id[]' value='0' /><input type='hidden' name='answer_order[]' value='"+$newOrder+"' /></td>";
            $html +="</tr>";
            $html +="</tbody>";
            $html +="</table>";
            $html +="</td></tr>";
            $tableObj.append($html);
        }


        $acc = $("h4."+$cat_row_id);

        $(".accordion-content").not($acc.next()).slideUp('fast');
        $(".accordion-toggle").not($cat_row_id).removeClass("active");
        $acc.addClass("active");



        $acc.next().slideDown("fast");

        $noAnswerFloat = template=="step_ec_sentences" ? 'style="float:none;"' : "";

        //update preview
        $acc.next(".accordion-content").find(".options-holder").find(".steps-answer-holder").append('<div class="step-answer temp-row-'+$newOrder+'" '+$noAnswerFloat+'><label><input type="checkbox" data-catrow="answer-row-cat-'+$answer_cat_id+'" value="1" class="selectoption" onclick="toggleOptionActive($(this))" /><span>'+$answer+'</span></label></div>');
        updateExposureStepPreview();

        $("#answer-list-table-ec").find(".no-answers").hide();


         $("#step_answer").val("").focus();
    }

}


function removeExposureAnswerRowCat(obj){

    if(confirm("Are you sure you want to delete this category? This will delete its answers too.")){
             $cls = obj.attr("data-rowid");

             var dowhat = obj.attr("data-dowhat");

            if(dowhat==1){
                $dowhat = 0;
            } else{
                $dowhat = 1;
            }

            $cat_id = $("."+$cls).find("input[name='cat_id[]']").val();
            if($cat_id>0){ //needed only for deleting row that comes from database

                $.ajax({
                    url: $sitePath + "/minapp/admin/removeExposureStepAnswerCat",
                    type: "post",
                    data: "cat_id="+$cat_id+"&dowhat="+$dowhat,
                    beforeSend: function(){
                        $("#preLoader").show();
                    },
                    success: function(data){
                        $("#preLoader").hide();
                        if($.trim(data)!=="success"){
                            return false;
                        }
                    }
                });

            }

            $("."+$cls).fadeOut("slow", function(){
                $(this).remove();
            });
            
             $("#answer-list-table-ec").find("tr[data-catid='"+$cls+"']").fadeOut("slow", function(){
                    $(this).remove();
             });


           /* if($dowhat==0){
                $img = "<img src='../../images/admin_icons/enabled.gif'/>";
                $newdowhat = 0;
                //$("."+$cls).addClass("red-row-cat");
                $("."+$cls).fadeout("slow", function(){
                    $(this).remove();
                });
                // $("#answer-list-table-ec").find("tr[data-catid='"+$cls+"']").addClass("red-row");
                 $("#answer-list-table-ec").find("tr[data-catid='"+$cls+"']").fadeout("slow", function(){
                        $(this).remove();
                 });
           }else{
                $newdowhat = 1;
                $img = "<img src='../../images/admin_icons/delete.png'/>";
                $("."+$cls).removeClass("red-row-cat");
                 $("#answer-list-table-ec").find("tr[data-catid='"+$cls+"']").removeClass("red-row");
           }*/


            $("h4."+$cls).next().remove();

          /*  obj.attr("data-dowhat",$newdowhat);
            obj.html($img);*/


            if($("#answer-list-table-ec").find("tbody tr:not('.no-answers')").length==0){
                $("#answer-list-table-ec").find(".no-answers").show();
            }

            $("#answer_cat_selector option[value='"+$cat_id+"']").remove();

            $tableObj = $("#answer-list-table-cat").find("tbody").find("tr").not(".no-answers").length;
           if($tableObj==0){
                $("#answer-list-table-cat").find(".no-answers").show();
           }
            updateExposureStepPreview();
    }

   
}


function editExposureAnswerRowCat(obj){
    $cls = obj.attr("data-rowid");

    obj.addClass("hide");
    $td = $("."+$cls).find(".td-answers-cat");
    $answer = $.trim($td.find("span").text());

    $td.html("<div class='edit-"+$cls+"'><input type='text' value='"+$answer+"' class='inputs w85'/>&nbsp;&nbsp;<a href='javascript:void(0)' data-rowid='"+$cls+"' onclick='updateExposureAnswerCat($(this))'><img src='../../images/admin_icons/tick.gif'/></a></div>")

}


function updateExposureAnswerCat(obj){
    $cls = obj.attr("data-rowid");
    $td = $("."+$cls).find(".td-answers-cat");
    $div = $td.find(".edit-"+$cls);
    $newanswer = $div.find("input[type='text']").val();
    $td.html("<input type='hidden' name='answers_category[]' value='"+$newanswer+"' /><span>"+$newanswer+"</span>");
    $atag = $td.next("td").find(".edit-row-cat").removeClass("hide");
    $div.remove();
    $(".phone-preview h4."+$cls).html($newanswer);
}


function removeExposureAnswerRowEc(obj){
    if(confirm("Are you sure you want to delete this answer?")){
        $cls = obj.attr("data-rowid");

        var dowhat = obj.attr("data-dowhat");

        if(dowhat==1){
            $dowhat = 0;
        } else{
            $dowhat = 1;
        }

        $answer_id = $("."+$cls).find("input[name='answer_id[]']").val();
        if($answer_id>0){ //needed only for deleting row that comes from database

            $.ajax({
                url: $sitePath + "/minapp/admin/removeExposureStepAnswer",
                type: "post",
                data: "answer_id="+$answer_id+"&dowhat="+$dowhat,
                beforeSend: function(){
                    $("#preLoader").show();
                },
                success: function(data){
                    if($.trim(data)!=="success"){
                        return false;
                    }
                }
            });
        }

        if($dowhat==0){
            $img = "<img src='../../images/admin_icons/enabled.gif'/>";
            $newdowhat = 0;
            //$("."+$cls).addClass("red-row");
            $("."+$cls).fadeOut("slow", function(){
                    $(this).remove();
            });
       }else{
            $newdowhat = 1;
            $img = "<img src='../../images/admin_icons/delete.png'/>";
            //$("."+$cls).removeClass("red-row");
             $("."+$cls).fadeOut("slow", function(){
                    $(this).remove();
            });
       }

        //If all answers for category has been removed then remove category heading as well in answer table.
        $sel = $("."+$cls).parent("tbody");

        obj.attr("data-dowhat",$newdowhat);
        obj.html($img);

        if($sel.find("tr").length==0){
            $sel.parent("table").closest("tr").remove();
        }

        $tableObj = $("#answer-list-table-ec").find("tbody.main").find("tr").not(".no-answers").length;
       if($tableObj==0){
            $(".no-answers").show();
       }
        updateExposureStepPreview(); 
    }
    
}


function editExposureAnswerRow(obj){
    $cls = obj.attr("data-rowid");
    obj.addClass("hide");
    $td = $("."+$cls).find(".td-answers");
    $answer = $.trim($td.find("span").text());
    $td.html("<div class='edit-"+$cls+"'><input type='text' value='"+$answer+"' class='inputs w85'/>&nbsp;&nbsp;<a href='javascript:void(0)' data-rowid='"+$cls+"' onclick='updateExposureAnswer($(this))'><img src='../../images/admin_icons/tick.gif'/></a></div>")
}


function updateExposureAnswer(obj){
    $cls = obj.attr("data-rowid");
    $td = $("."+$cls).find(".td-answers");
    $div = $td.find(".edit-"+$cls);
    $newanswer = $div.find("input[type='text']").val();
    $td.html("<input type='hidden' name='answers[]' value='"+$newanswer+"' /><span>"+$newanswer+"</span>");
    $atag = $td.next("td").find(".edit-row").removeClass("hide");
    $div.remove();
    $(".phone-preview .options-holder").find("."+$cls).find("label span").html($newanswer);
}


//function for the Expand collapse
function AddExposureECDescription(){

    $answer_cat = $.trim($("#step_answer_cat").val());
    $content = $.trim($("#step_answer_content").val());

    //to add answer category one should provide step title so that we can save category to database.
    if($.trim($("#step_title").val())=="" && $("#step_id").val()==0){
        alert($jsLang["specify_step_title"]);
        return false;
    }
    //check if same category already added.
    $test = $.inArray($answer_cat.toLowerCase(),setArrayFromInput("answers_category[]"));
    if($test>-1){
        alert($jsLang["item_in_list"]);
        return false;
    }

    if($.trim($answer_cat)!=""){
        //save step to the database first
        $.ajax({
            url: $sitePath + "/minapp/admin/saveExposureAnswerCategoryForDescription",
            data: $("#frmAddExposureSteps").serialize(),
            type: "POST",
            beforeSend:function(){ $("#preLoader").show();},
            success: function(data){
                var d = $.parseJSON(data);
                $("#preLoader").hide();
                if(parseInt(d.step_id)>0){
                    $("#step_id").val(d.step_id);
                    if(d.new_step===1){
                         $class = (d.sort_order%2==1)?"even":"odd";
                       /*  $("#step-list-table").find("tbody  tr.no-steps").remove();
                         //$no_data_row.
                         $("#step-list-table").append("<tr id='ID_"+d.step_id+"' class='"+$class+" step-row'>" +
                                                 "<td class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>" +
                                                 "<td class='handle' data-stepid='"+d.step_id+"'>"+$("#step_title").val()+"</td>"+
                                                 "<td class='handle'>"+d.template_name+"</td>" +
                                                "<td><a href='javascript:void(0)' data-stepid='"+d.step_id+"' data-newstatus='0' class='link-green change-step-status' title='Change Status to Inactive'><img src='../../images/admin_icons/enabled.gif'></a></td>"+
                                                "<td><a href='javascript:void(0)' onclick=\"showSelectedStep('"+d.skill_id+"','"+d.flow_id+"','"+d.template+"',"+d.step_id+")\"><img src='../../images/admin_icons/edit.png'></a></td>"
                                                 );*/
                    }


                    $tableObj = $("#answer-cat-list-table").find("tbody");
                    $rowCount = $tableObj.find("tr").not(".no-answers").length;
                    //$newOrder = (d.sort_order>0)?d.sort_order: $rowCount+1;
                    $newOrder = d.cat_id;
                    $html = "<tr data-row='row-answer-cat' class='answer-row-cat-"+$newOrder+"'>";
                    $cls = "answer-row-cat-"+$newOrder;
                    $html +="<td class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>";
                    $html +="<td class='td-answers-cat handle'><input type='text' name='answers_category[]' class='inputs' style='display:none;width:200px;' value='"+$answer_cat+"' /><span>"+$answer_cat+"</span></td>";
                    $html +="<td class='cat-contents'><input type='hidden' name='answer_id[]' value='"+d.answer_id+"' /><textarea name='answers[]' rows='4' cols='35' style='display:none'>"+$content+"</textarea><span>"+$content+"</span></td>";
                    $html +="<td><a href='javascript:void(0)' class='remove-row-cat' data-rowid='"+$cls+"' onclick='removeExposureAnswerRowCat($(this))'><img src='../../images/admin_icons/delete.png'/></a>&nbsp;&nbsp;&nbsp;"+
                    "<a href='javascript:void(0)' class='edit-row-cat' data-isdescription='yes' data-rowid='"+$cls+"' onclick='editExposureDescriptionAnswerRowCat($(this))'><img src='../../images/admin_icons/edit.png'/></a>" +
                    "<a href='javascript:void(0)' style='display:none;' class='edit-row-cat-finished' data-isdescription='yes' data-rowid='"+$cls+"' onclick='finishEditingExpandCollapseExposureDescription($(this))'><img src='../../images/admin_icons/tick.gif'/></a>" +
                    "<input type='hidden' name='cat_id[]' value='"+d.cat_id+"' /><input type='hidden' name='answer_cat_order[]' value='"+d.cat_sort_order+"' /></td>";
                    $html +="</tr>";

                    $divcontent = "<div class='answer-cat-content'>"+$content+"</div>";
                    $greenbutton = "<div class='accept-btn-holder' style='margin-top:10px;margin-bottom:5px;'><a href='javascript:void(null)' class='new-btn' style='width:91%;display:inline-block;text-align:center;'>Den här beskrivningen stämmer</a></div>"
                    $tableObj.append($html);

                    $(".phone-preview").find("#accordion").append('<h4 class="accordion-toggle active answer-row-cat-'+$newOrder+'"  onclick="toggleAccordion($(this))">'+$answer_cat+'</h4>'+
                              '<div class="accordion-content">'+
                                '<div class="options-holder keywords">'+
                                    '<div class="steps-answer-holder">'+$divcontent+$greenbutton+
                                    '</div>'+
                                '</div>'+
                                '<div class="clear"></div>'+
                              '</div>');
                    $acc = $(".answer-row-cat-"+$newOrder);

                    $(".accordion-content").not($acc.next()).slideUp('fast');
                    $(".accordion-toggle").not($cls).removeClass("active");
                    $("h4."+$cls).addClass("active");
                    $acc.next().slideToggle("fast");

                    updateExposureStepPreview();


                    $("#answer-cat-list-table").find(".no-answers").hide();

                    $("#answer_cat_selector").append("<option data-rowid='"+$cls+"' value='"+d.cat_id+"'>"+$answer_cat+"</option>");

                    $("#step_answer_cat").val("").focus();
                    $("#step_answer_content").val("");
                    tinyMCE.activeEditor.setContent('');
                }

            }
        });


    }
}

function editExposureDescriptionAnswerRowCat(obj){
    $answer_cat = obj.parents("tr").find("td.td-answers-cat");
    $content_cat = obj.parents("tr").find("td.cat-contents");

    obj.next(".edit-row-cat-finished").show();

    $answer_cat.find("input").show();
    $answer_cat.find("span").hide();

    $content_cat.find("textarea").show();
    $content_cat.find("span").hide();

}

function finishEditingExpandCollapseExposureDescription(obj){
    $answer_cat = obj.parents("tr").find("td.td-answers-cat");
    $content_cat = obj.parents("tr").find("td.cat-contents");

    $answer_cat.find("input").hide();
    $answer_cat.find("span").html($answer_cat.find("input").val()).show();

    $content_cat.find("textarea").hide();
    $content_cat.find("span").html($content_cat.find("textarea").val()).show();

    obj.hide();
}

function AddNewExposureAnswer(){
    $answer = $("#step_answer").val();

    $test = $.inArray($answer.toLowerCase(),setArrayFromInput("answers[]"));
    if($test>-1){
        alert($jsLang["item_in_list"]);
        return false;
    }

    if(arguments[0]=="step_keywords"){
        $fl = "mfl";
    }else{
        $fl = "";
    }

    if($.trim($answer)!=""){
        $tableObj = $("#answer-list-table").find("tbody");
        $rowCount = $tableObj.find("tr").not(".no-answers").length;
        $newOrder = $rowCount+1;
        $html = "<tr data-row='row-answer' class='temp-row-"+$newOrder+"'>";
        $cls = "temp-row-"+$newOrder;
        $html +="<td class='handle'><img src='../../images/admin_icons/reorder.png' width='18' / ></td>";
        $html +="<td class='td-answers handle'><input type='hidden' name='answers[]' class='inputs w100' value='"+$answer+"' /><span>"+$answer+"</span></td>";
        $html +="<td><a href='javascript:void(0)' class='remove-row' data-rowid='"+$cls+"' onclick='removeAnswerRow($(this))'><img src='../../images/admin_icons/delete.png'/></a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='edit-row' data-rowid='"+$cls+"' onclick='editAnswerRow($(this))'><img src='../../images/admin_icons/edit.png'/></a><input type='hidden' name='answer_cat_id[]' value='0' /><input type='hidden' name='answer_id[]' value='0' /><input type='hidden' name='answer_order[]' value='"+$newOrder+"' /></td>";
        $html +="</tr>";

        $tableObj.append($html);
        //update preview
        $(".phone-preview").find(".options-holder").append('<div class="step-answer '+$fl+' temp-row-'+$newOrder+'"><label><input type="checkbox" value="1" class="selectoption" onclick="toggleOptionActive($(this))" /><span>'+$answer+'</span></label></div>');
        //updateExposureStepPreview();

        $(".no-answers").hide();


         $("#step_answer").val("").focus();
    }
}

function removeExposureAnswerRow(obj){

    $cls = obj.attr("data-rowid");

    var dowhat = obj.attr("data-dowhat");

    if(dowhat==1){
        $dowhat = 0;
    } else{
        $dowhat = 1;
    }

    $answer_id = $("."+$cls).find("input[name='answer_id[]']").val();
    if($answer_id>0){ //needed only for deleting row that comes from database

        $.ajax({
            url: $sitePath + "/minapp/admin/removeExposureStepAnswer",
            type: "post",
            data: "answer_id="+$answer_id+"&dowhat="+$dowhat,
            beforeSend: function(){
                $("#preLoader").show();
            },
            success: function(data){
                if($.trim(data)!=="success"){
                    return false;
                }
            }
        });

    }

   if($dowhat==0){
        $img = "<img src='../../images/admin_icons/enabled.gif'/>";
        $newdowhat = 0;
        $("."+$cls).fadeOut("slow",function(){
            $(this).remove();
        });
        $("."+$cls).addClass("red-row");

   }else{
        $newdowhat = 1;
        $img = "<img src='../../images/admin_icons/delete.png'/>";
        $("."+$cls).removeClass("red-row");
   }


   obj.attr("data-dowhat",$newdowhat);
   obj.html($img);

    $tableObj = $("#answer-list-table").find("tbody").find("tr").not(".no-answers").length;
   if($tableObj==0){
        $(".no-answers").show();
   }
    updateExposureStepPreview();
}

//ADDED BY SABIN on 12th August 2015 <<


/*Added by Sabin @ 6th October 2015 >>*/
function deleteSelectedStep(stepID,inUse){
     var deleteStep = false;
    if(confirm("Are you sure you want to delete this registration step?")){
        if(inUse==true){
            if(confirm("This registration step is already in use. Do you want to delete this?")){
                deleteStep = true;
            }
        }else{
            deleteStep = true;
        }

        if(deleteStep==true){ //now delete the registration

            var newStatus = 0;
            $.ajax({
                url: $sitePath + "/minapp/admin/changeRegStepStatus",
                type: "POST",
                async:false,
                data: "step_id="+stepID+"&new_status="+newStatus,
                beforeSend:function(){
                     $("#preLoader").show();
                },
                success:function(data){
                    $("#preLoader").hide();
                    $("#step-list-table").find("tr#ID_"+stepID).fadeOut("slow",function(){
                        $(this).remove();
                    });
                }
            });
        }
    }
}
/*Added by Sabin @ 6th October 2015 <<*/

/*Added by Sabin @ 27th October 2015 >>*/
function enableDisableCountdownElements(){

    $chk = $("#enable_countdown").prop("checked");

   $("#cntdown_min_minutes").val($("#cntdown_min_minutes").attr("data-value"));
    $("#cntdown_max_minutes").val($("#cntdown_max_minutes").attr("data-value"));

     /*$("#cntdown_start_desc").val($("#cntdown_start_desc").attr("data-value"));
    $("#cntdown_countdown_desc").val($("#cntdown_countdown_desc").attr("data-value"));
    $("#cntdown_start_title").val($("#cntdown_start_title").attr("data-value"));*/

    if($chk==true){
        $(".div-countdown-elements").show();
    }else{
        $(".div-countdown-elements").hide();
    }
}

function enableDisableCountdown(){
    $chk = $("input[name='is_multiple_choice']").prop("checked");

    if($chk==true){
        $(".div-enable-countdown").show();
        if($("#enable_countdown").attr("data-state")==1){
            $("#enable_countdown").prop("checked",true);
        }
    }else{
        $(".div-enable-countdown").hide();
        $("#enable_countdown").prop("checked",false);
    }
    enableDisableCountdownElements();
}
/*Added by Sabin @ 27th October 2015 <<*/
