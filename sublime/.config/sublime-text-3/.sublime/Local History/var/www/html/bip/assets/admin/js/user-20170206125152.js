var rememberFilter={};
rememberFilter.current_page=1;
var countdown = {
    startInterval: function() {
        if (readCookie("timer") != undefined) {
            var count = readCookie("timer") - 1;
        } else {
            var count = $timer; // minute timeout based on database stored timer value
        }
        var currentId = setInterval(function() {
            createCookie("timer", count, 365);
            var clockTime = secondsToHms(count);
            // console.log(clockTime);
            $('#runtime').html(clockTime).show();
            var trackTime = secondsToHms($timer - count);
            $('#passtime').html(trackTime).show();

            if (count > 0 && count % 60 === 0 && count != $timer) { //to prevent accidental loss of time tracking , per minute ajax time update
                // console.log('do accidental update'+count);
                createCookie("acc_timer", count, 365);
                ajax_accidental_update($patient_id, 60);
                if (count == 120) { // when there's 2 minutes left,prompting user to inform timeout
                    $.confirm({
                        'title': $jsLang['inactive_soon_logout'],
                        'message': '',
                        'buttons': {
                            'yes': {
                                'class': 'blue',
                                'button_name': $jsLang['yes'],
                                'action': function() {
                                    $(this).dialog("close");
                                }
                            },
                            'no': {
                                'class': 'gray',
                                'button_name': $jsLang['no'],
                                'action': function() {
                                        clearInterval(countdown.intervalId);
                                        var psycho_time = readCookie('acc_timer') - readCookie("timer");
                                        ajax_time_update($patient_id, psycho_time, 'logout');
                                    } // Nothing to do in this case. You can as well omit the action property.
                            }
                        }
                    });
                }
            }
            if (count <= 0) { //if counter zero
                clearInterval(countdown.intervalId);
                ajax_time_update($patient_id, 60, 'logout');
            }
            --count;
        }, 1000); // 1 second interval
        countdown.intervalId = currentId;
    }
};

$(function() {
    if (typeof(patient_id) !== "undefined" && !$patient_id) {
        eraseCookie("patient_id");
        eraseCookie("timer");
        eraseCookie("acc_timer");
    }

    if ($usertype == 'Psychologist') {
        var patient_id = $patient_id;
        if (isNumber(patient_id)) {
            createCookie("patient_id", patient_id, 365);
            if (readCookie("patient_id") != undefined && isNumber(readCookie("patient_id"))) {
                var ignorePageArr = ['faq', 'user'];
                if (jQuery.inArray($segmentPage, ignorePageArr) !== -1) { //is in array
                    clearInterval(countdown.intervalId);
                } else {
                    countdown.startInterval();
                }
            }
            //to stop user tracking
            $(".user_check_tab").click(function(e) {
                var user_id = $(this).attr('rel');
                if (readCookie("patient_id") != undefined && readCookie("patient_id") != user_id) {
                    e.preventDefault();
                    trackDialogForm(user_id);
                }
            });

        }

        // modified logout from controller for psychology time track
        $('.logout a').click(function(e) {
            if (readCookie("patient_id") != undefined && readCookie('timer') != undefined) {
                e.preventDefault();
                trackDialogForm('logout');
            }
        });


        // modified admin from controller for psychology time track
        $('#psytoadmin').click(function(e) {
            if (readCookie("patient_id") != undefined && readCookie('timer') != undefined) {
                e.preventDefault();
                trackDialogForm('admin');
            }
        });

        //to stop user tracking
        $('.psycho #stage a,.fulltext a').click(function(e) {
            if (readCookie("patient_id") != undefined && readCookie('timer') != undefined) {
                e.preventDefault();
                trackDialogForm('overview');
            }
        });

        //to stop changing the view without selecting patitent
        $('.stopClick').click(function(e) {
            if (readCookie('timer')==null) {
                e.preventDefault();
                $warning_title = $jsLang['choose_patient'];
                alert($warning_title);return false;
            }
        });

    }

    if ($usertype == 'user') {
        // unique page views counter
        if (typeof $currentPage !== 'undefined') {
            $currentPage = $currentPage.replace(/ |\//g, "_");
            ajax_page_views_update($currentUserId, $currentPage);
        }
    }

});
var groupid=0;
$(function(){
    groupid=$('#group').val();
})
function getSelectedGrpId(grpids,sel_psy,initial){
    //console.log(sel_psy);
   if(grpids!=undefined)
    var grparr=grpids.split(",");
else{
    grparr=[];
}
    //debugger;
    if ($usertype == 'admin') {
        $path = $sitePath + "/user/admin/get_selected_grp";
    } else {
        $path = $sitePath + "/user/user/get_selected_grp";
    }
    var selected_grp_id = $('#group').val();
    if(initial!=false){
        if(grparr.indexOf(selected_grp_id)==-1 && selected_grp_id != 0){
            if(!confirm("You don't have permission on the selected group. Do you still want to continue?")){
                        //alert("hello");die;
                        $("#group").val(groupid);
                        return false;

                    }
                }
            }
     $.ajax({
        type: 'post',
        url: $path,
        async: true,
        data: {
            "selected_grp_id": selected_grp_id
        },
        success: function(response) {
            groupid=selected_grp_id;
          /* if ($usertype == 'Psychologist')
                $('#tab1').html(response);
            else
                $('#content').html(response);
            $('#group').val(selected_grp_id);*/

            $('#psychologist').html('');
                    response=JSON.parse(response);
                    $('#psychologist').append("<option value='0'>Choose Psychologist</option>")
                    var arrids=[];
                    response.forEach(function(x){
                        if(arrids.indexOf(x.id)==-1){
                        arrids.push(x.id);
                            $('#psychologist').append("<option value='"+x.id+"'>"+x.first_name+" "+x.last_name+"</option>")
                        }
                    })
                    if(sel_psy){
                     $('#psychologist').val(sel_psy);
                    }

        }
    });
}

function trackDialogForm(respType) {
    // console.log("response clicked is " + respType);
    $("#user-input").livequery(function() {
        $(this).setMask('23:59:59');
    });
    if($unsaved == true){
        $warning_title = $jsLang['unsave_data'];
    }else{
        $warning_title = '';
    }


    $.confirm({
        warning_title: $warning_title,
        title: $jsLang['change_time_if_necessary'],
        'message': secondsToHms($timer - readCookie('timer')),
        'buttons': {
            'save': {
                'class': 'blue',
                'button_name': $jsLang['save'],
                'action': function() {
                    $unsaved = false;
                    // console.log("track dialog form save clicked ");
                    clearInterval(countdown.intervalId);

                    if (readCookie('acc_timer') != undefined) {
                        var accUpdatedTime = $timer - readCookie('acc_timer');
                    } else {
                        var accUpdatedTime = 0;
                    }
                    var inputTime = hmsToSecondsOnly($("#user-input").val());
                    var psycho_time = inputTime - accUpdatedTime;


                    if (readCookie("patient_id") != undefined && readCookie('timer') != undefined) {
                        var patient_id = readCookie("patient_id");
                        // console.log("timer: " + readCookie('timer') + " patient_id: " + readCookie('patient_id'));
                        ajax_time_update(patient_id, psycho_time, respType);
                    }
                    else {
                        alert("Something wrong with time tracker.Please contact the BIP support.");
                    }
                }
            },
            'close': {
                'class': 'gray',
                'button_name': $jsLang['close'],
                'action': function() {
                        var newTime = readCookie("timer") - 1;
                        createCookie("timer", newTime, 365);
                    } // Nothing to do in this case. You can as well omit the action property.
            }
        }
    });
}


function hmsToSecondsOnly(str) {
    var p = str.split(':'),
        s = 0,
        m = 1;

    while (p.length > 0) {
        s += m * parseInt(p.pop(), 10);
        m *= 60;
    }

    return s;
}

function secondsToHms(d) {
    var totalSec = Number(d);
    var hours = parseInt(totalSec / 3600) % 24;
    var minutes = parseInt(totalSec / 60) % 60;
    var seconds = totalSec % 60;

    return result = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
}

function ajax_page_views_update(user_id, page) {
    $.ajax({
        url: $sitePath + "/stage/stage/update_page_views",
        async: true,
        type: 'post',
        data: {
            "user_id": user_id,
            "page": page
        },
        success: function(response) {
            if (response == 'success') {
                createCookie("currentPage", $currentPage, 365);
            }
        }
    });
}

function ajax_accidental_update(p_id, psycho_time) {
    $.ajax({
        url: $sitePath + "/stage/stage/update_spent_time",
        async: true,
        type: 'post',
        data: {
            "patient_id": p_id,
            "time": psycho_time
        },
        success: function(response) {
            if (response == 'success') {
                // console.log(response);
            }
        }
    });
}

function ajax_time_update(p_id, psycho_time, respType) {
    // console.log("ajax_time_update function called ");
    $.ajax({
        url: $sitePath + "/stage/stage/update_spent_time",
        async: true,
        type: 'post',
        data: {
            "patient_id": p_id,
            "time": psycho_time
        },
        success: function(response) {
            // if (response == 'success') {
            eraseCookie("timer");
            eraseCookie("acc_timer");
            if (respType == 'logout') {
                // console.log("time update response " + response);
                eraseCookie("patient_id");
                eraseCookie("timer");
                eraseCookie("acc_timer");
                window.location.href = $sitePath + "/login/logout";
            } else if (respType == 'admin') {
                window.location.href = $sitePath + "/stage/admin";
            } else if (respType == 'overview') {
                window.location.href = $sitePath + "/stage/overview";
            } else {
                window.location.href = $sitePath + "/stage/user/" + respType;
            }
            // }
        }
    });
}

function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    } else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

function getstageforuser(patient_id) {
    // alert(patient_id);return false;
    $("#selectdata").val(patient_id);
    $.ajax({
        type: 'post',
        url: $sitePath + "/stage/stage/getstageforuser",
        async: true,
        data: {
            "patient_id": patient_id
        },
        success: function(response) {
            var data = response.split('|~|~|');
            $('#box1-tabular').html(data[1]);

        }
    });
}

$( document ).ajaxComplete(function() {

    $('.pagination a').each(function(){
         var event=$(this).attr("onclick");
         if(event!=""){
             $(this).attr('data-onclick',event);
             $(this).attr("onclick",'');

         }
    })

    $('.pagination a').click(function(){
        rememberFilter={difficulty_id:$('#filter_difficulty').val(),current_page:-1,psychologist_id:$('#filter_psychologist').val(),group_id:$('#filter_group').val(),search_text:$('#search_text').val()};
        var event=$(this).attr("data-onclick");
        eval(event);

    })

});


$(function(){

    $('.pagination a').each(function(){
         var event=$(this).attr("onclick");
         if(event!=""){
             $(this).attr('data-onclick',event);
             $(this).attr("onclick",'');

         }
    })


    $('.pagination a').click(function(){
        rememberFilter={difficulty_id:$('#filter_difficulty').val(),current_page:-1,psychologist_id:$('#filter_psychologist').val(),group_id:$('#filter_group').val(),search_text:$('#search_text').val()};
        var event=$(this).attr("data-onclick");
        eval(event);

    })

})

function listUser(orderBy, offset) {
    if (offset == undefined || offset == 0) {
        offset = 0;
    }
    if (orderBy == undefined || orderBy == '') {
        orderBy = 'id asc ';
    }
    if ($usertype == 'admin') {
        $path = $sitePath + "/user/admin/listAllUser";
    } else {
        $path = $sitePath + "/user/user/listAllUser";
    }
    var psychologist_id = $('#filter_psychologist').val(),
        difficulty_id = $('#filter_difficulty').val(),
        group_id = $('#filter_group').val();
        search_text = $('#search_text').val();
        if(rememberFilter.current_page!=-1 && rememberFilter.current_page!='' )
            offset=(parseInt(rememberFilter.current_page)-1)*100;
    $.ajax({
        type: 'post',
        url: $path,
        async: true,
        data: {
            "offset": offset,
            "orderBy": orderBy,
           "psychologist_id": rememberFilter.psychologist_id,
            "difficulty_id": rememberFilter.difficulty_id,
            "group_id": rememberFilter.group_id,
            "search_txt": rememberFilter.search_text
        },
        success: function(response) {
            $('#content').html(response);
            $('#filter_difficulty').val(rememberFilter.difficulty_id);
            $('#filter_psychologist').val(rememberFilter.psychologist_id);
            $('#filter_group').val(rememberFilter.group_id);
            $('#search_text').val(rememberFilter.search_text);
    }
    });
}

function listUserByPsychologist(orderBy, offset,psychologist_id) {
    if (offset == undefined || offset == 0) {
        offset = 0;
    }
    if (orderBy == undefined || orderBy == '') {
        orderBy = 'first_name asc ';
    }
    if ($usertype == 'admin') {
        $path = $sitePath + "/user/admin/listUserByPsychologist";
    } else {
        $path = $sitePath + "/user/user/listUserByPsychologist";
    }
    $.ajax({
        type: 'post',
        url: $path,
        async: true,
        data: {
            "offset": offset,
            "orderBy": orderBy,
            "psychologist_id": psychologist_id,
            "difficulty_id": difficulty_id,
            "group_id": group_id
        },
        success: function(response) {

            // if ($usertype == 'Psychologist') {
            //     $('#tab1').html('');
            //     $('#tab1').html(response);
            //     $("#topleaveforedit").addClass("topleaveforedit");
            // } else
                // $("#topleaveforedit").addClass("topleaveforedit");
            $('#content').html(response);
            $('#psychology').val(psychologist_id);

        }
    });
}

function filterUserByParams(orderBy, offset) {
    rememberFilter={difficulty_id:$('#filter_difficulty').val(),current_page:-1,psychologist_id:$('#filter_psychologist').val(),group_id:$('#filter_group').val(),search_text:$('#search_text').val()};
    if (offset == undefined || offset == 0) {
        offset = 0;
    }
    if (orderBy == undefined || orderBy == '') {
        orderBy = 'first_name asc ';
    }
    if ($usertype == 'admin') {
        $path = $sitePath + "/user/admin/filterUserByParams";
    } else {
        $path = $sitePath + "/user/user/filterUserByParams";
    }

    var psychologist_id = $('#filter_psychologist').val(),
        difficulty_id = $('#filter_difficulty').val(),
        group_id = $('#filter_group').val();
        search_text=$('#search_text').val();

        if(rememberFilter.current_page!=-1 && rememberFilter.current_page!='' )
            offset=(parseInt(rememberFilter.current_page)-1)*100;

        //console.log(psychologist_id,difficulty_id,group_id,search_text)

    $.ajax({
        type: 'post',
        url: $path,
        async: true,
        data: {
            "offset": offset,
            "orderBy": orderBy,
            "psychologist_id": rememberFilter.psychologist_id,
            "difficulty_id": rememberFilter.difficulty_id,
            "group_id": rememberFilter.group_id,
            "search_txt": rememberFilter.search_text
        },
        success: function(response) {

            // if ($usertype == 'Psychologist') {
            //     $('#tab1').html('');
            //     $('#tab1').html(response);
            //     $("#topleaveforedit").addClass("topleaveforedit");
            // } else
                // $("#topleaveforedit").addClass("topleaveforedit");
            $('#content').html(response);

            $('#filter_difficulty').val(rememberFilter.difficulty_id);
            $('#filter_difficulty').val(difficulty_id);
            $('#filter_psychologist').val(psychologist_id);
            $('#filter_group').val(group_id);

        }
    });
}

function listUserByDifficulty(orderBy, offset,difficulty_id) {
    if (offset == undefined || offset == 0) {
        offset = 0;
    }
    if (orderBy == undefined || orderBy == '') {
        orderBy = 'first_name asc ';
    }
    if ($usertype == 'admin') {
        $path = $sitePath + "/user/admin/listUserByDifficulty";
    } else {
        $path = $sitePath + "/user/user/listUserByDifficulty";
    }
    $.ajax({
        type: 'post',
        url: $path,
        async: true,
        data: {
            "offset": offset,
            "orderBy": orderBy,
            "psychologist_id": psychologist_id,
            "difficulty_id": difficulty_id,
            "group_id": group_id
        },
        success: function(response) {

            // if ($usertype == 'Psychologist') {
            //     $('#tab1').html('');
            //     $('#tab1').html(response);
            //     $("#topleaveforedit").addClass("topleaveforedit");
            // } else
                // $("#topleaveforedit").addClass("topleaveforedit");
            $('#content').html(response);
            $('#difficulty').val(difficulty_id);

        }
    });
}

function listUserByGroup(orderBy, offset,group_id) {
    if (offset == undefined || offset == 0) {
        offset = 0;
    }
    if (orderBy == undefined || orderBy == '') {
        orderBy = 'first_name asc ';
    }
    if ($usertype == 'admin') {
        $path = $sitePath + "/user/admin/listUserByGroup";
    } else {
        $path = $sitePath + "/user/user/listUserByGroup";
    }
    $.ajax({
        type: 'post',
        url: $path,
        async: true,
        data: {
            "offset": offset,
            "orderBy": orderBy,
            "psychologist_id": psychologist_id,
            "difficulty_id": difficulty_id,
            "group_id": group_id
        },
        success: function(response) {

            // if ($usertype == 'Psychologist') {
            //     $('#tab1').html('');
            //     $('#tab1').html(response);
            //     $("#topleaveforedit").addClass("topleaveforedit");
            // } else
                // $("#topleaveforedit").addClass("topleaveforedit");
            $('#content').html(response);
            $('#group').val(group_id);

        }
    });
}

function checkUsername(username) {
    if ($usertype == 'admin') {
        $path = $sitePath + "/user/admin/checkUsername";
    } else {
        $path = $sitePath + "/user/user/checkUsername";
    }
	if (username && username != document.frmAddUser.oldUsername.value) {
        $.ajax({
            type: "POST",
            url: $path,
            data: {
					"username": username,
					"user_id": $('#hidid').val()
            },
            success: function(response) {
                //alert(response);

                $("#error_email").html("");
                if (response == "1") {
                    $("#checkEmail").val("");
                    $("#email").addClass("error");
                    $("#error_email").html('<label class="error">Användaren finns redan.</label>');
                } else {
                    $("#checkEmail").val("1");
                    // $("#error_email").html('<span style="color:#5DA93E">Anv&#228;ndaren &#228;r inte tillg&#228;nglig !</span>');
                }
            },
            error: function() {
                // alert("error occured");
            }
        });
	} else return false;
}


function addUserForm() {
    if ($usertype == 'admin') {
        $path = $sitePath + "/user/admin/addUserForm";
    } else {
        $path = $sitePath + "/user/addUserForm";
    }
    $.ajax({
        type: 'post',
        url: $path,
        async: true,
        data: {},
        success: function(response) {
            if ($usertype == 'Psychologist')
                $('#tab1').html(response);
            else
                $('#content').html(response);

            $('#from').val('');
            $('#to').val('');
            $("#frmAddUser").validate({
                rules: {
                    username:  {
                        required: true
                    },
                    firstName: {
                        required: true
                    },
                    lastName: {
                        required: true
                    },
                    userType: {
                        required: true
                    },
                    //email:{required:true, email:true},
                    autogeneratedpw: {
                        required: true,
                        minlength: 5
                    },
                    from: {
                        required: true
                    },
                    to: {
                        required: true
                    }
                    //confirmPassword: {equalTo: "#password"},
                },
                messages: {
                    firstName: {
                        required: $jsLang['required']
                    },
                    lastName: {
                        required: $jsLang['required']
                    },
                    userType: {
                        required: $jsLang['required']
                    },
                    //email:{required:$jsLang['required']},
                    autogeneratedpw: {
                        required: $jsLang['required']
                    },
                    from: {
                        required: $jsLang['required']
                    },
                    //psychologist:{required:$jsLang['required']},
                    to: {
                        required: $jsLang['required']
                    }
                    //difficulty:{required:$jsLang['required']}

                }
            });



        }
    });
}

function addUser() {

    var $contact_number = $('#contact_number').val();
    var $email = $('#email').val();

    if ($email.length > 0)
        if (!isValidEmailAddress($email))
            $('#email').val('');

    var str = $("#frmAddUser").serialize();
    // str+="&psychologist_id="+rememberFilter.psychologist_id;
    // str+="&difficulty_id="+rememberFilter.difficulty_id;
    // str+="&group_id="+rememberFilter.group_id;
    str+="&search_txt="+rememberFilter.search_text;
    if(rememberFilter.current_page!=-1 && rememberFilter.current_page!='' )
    str+="&offset="+((parseInt(rememberFilter.current_page)-1)*100);
    if ($usertype == 'admin') {
        $path = $sitePath + "/user/admin/addUser";
    } else {
        $path = $sitePath + "/user/user/addUser";
    }

 //    $.blockUI({
	// 	message: '<h1>Updating User. Please be patient.</h1> '
	// });

    $.ajax({
        type: "Post",
        url: $path,
        data: str,
        async: false,
        beforeSend: function() {
            checkUsername($("#username").val());
            if (!$("#frmAddUser").valid()) {
                $("#preLoader").hide();
                return false;
            }
        },
        success: function(response) {

        	$("#preLoader").hide();
        	$.unblockUI();

            if (response) {
                $.facebox($jsLang['user_add_success']);

                $('#facebox').delay($fadeOutTime).fadeOut();
                $('#facebox_overlay').delay(500).fadeOut();

               /* if ($usertype == 'Psychologist') {
                    $('#tab1').html('');
                    $('#tab1').html(response);

                } else{
                    $('#content').html(response);
                }*/
				$('#filter_difficulty').val(rememberFilter.difficulty_id);
                $('#filter_psychologist').val(rememberFilter.psychologist_id);
                $('#filter_group').val(rememberFilter.group_id);
                $('#search_text').val(rememberFilter.search_text);

                listUser('id asc','');

            }
        }
    });
}


function editUser(userId) {
    rememberFilter={difficulty_id:$('#filter_difficulty').val(),current_page:$('.pagination li.selected a').text(),psychologist_id:$('#filter_psychologist').val(),group_id:$('#filter_group').val(),search_text:$('#search_text').val()};
    if ($usertype == 'admin') {
        $path1 = $sitePath + "/user/admin/editUser";
    } else {
        $path1 = $sitePath + "/user/user/editUser";
    }
    $.ajax({
        type: 'post',
        url: $path1,
        async: true,
        data: {
            "userId": userId
        },
        success: function(response) {
            if ($usertype == 'Psychologist')
                $('#tab1').html(response);
            else
                $('#content').html(response);

            $("#frmAddUser").validate({
                rules: {
                    username: { required: true },
                    firstName: {
                        required: true
                    },
                    lastName: {
                        required: true
                    },
                    userType: {
                        required: true
                    },
                    //email:{required:true, email:true},
                    autogeneratedpw: {
                        // required: true,
                        minlength: 5
                    },
                    from: {
                        required: true
                    },
                    to: {
                        required: true
                    }

                },
                messages: {
                    firstName: {
                        required: $jsLang['required']
                    },
                    lastName: {
                        required: $jsLang['required']
                    },
                    userType: {
                        required: $jsLang['required']
                    },
                    //email:{required:$jsLang['required']},
                    autogeneratedpw: {
                        required: $jsLang['required']
                    }


                }
            });

        }
    });
}



function deleteUser(userId) {
    if ($usertype == 'admin') {
        $path = $sitePath + "/user/admin/deleteUser";
    } else {
        $path = $sitePath + "/user/user/deleteUser";
    }

    if (!confirm($jsLang['alert_del_user'])) {
        return false;
    }
    $.ajax({
        type: 'post',
        url: $path,
        async: true,
        data: {
            "userId": userId
        },
        success: function(response) {
            if (response) {
                if ($usertype == 'Psychologist')
                    $('#tab1').html(response);
                else
                    $('#content').html(response);

                $.facebox($jsLang['user_del_success']);
                $('#facebox').delay($fadeOutTime).fadeOut();
                $('#facebox_overlay').delay(500).fadeOut();
            } else {
                $.facebox($jsLang['user_not_del_success']);
                $('#facebox').delay($fadeOutTime).fadeOut();
                $('#facebox_overlay').delay(500).fadeOut();
            }

        }
    });
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    var x = pattern.test(emailAddress);
    return x;
}

function isValidContactNumber(contact_number) {
    //var pattern = new RegExp(/^(([+]\d{2}[ ][1-9]\d{0,2}[ ])|([0]\d{1,3}[-]))((\d{2}([ ]\d{2}){2})|(\d{3}([ ]\d{3})*([ ]\d{2})+))$/);
    var pattern = new RegExp(/^((\d{3}[-])((\d{7})|(\d{10})))$/);
    var x = pattern.test(contact_number);
    return x;
}

function validateContactNumber(contact_number) {
    if (!isValidContactNumber(contact_number.value)) {
        $('#contact_number').css('border', '1px solid #FF0000');
        $('#error_contact_number').show();
    } else {
        $('#contact_number').css('border', '1px solid #CCCCCC');
        $('#error_contact_number').hide();
    }
}

function validateEmail(email) {
    if (!isValidEmailAddress(email.value)) {
        $('#email').css('border', '1px solid #FF0000');
        $('#error_email_user').show();
    } else {
        $('#email').css('border', '1px solid #CCCCCC');
        $('#error_email_user').hide();
    }
}
