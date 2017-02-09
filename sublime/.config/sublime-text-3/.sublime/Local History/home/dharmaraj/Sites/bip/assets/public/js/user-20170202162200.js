var rememberFilter={};
rememberFilter.current_page=1;

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
// 			function listUser(orderBy, offset) {

// 				if (offset == undefined || offset == 0 || !offset) {
// 					offset = 0;
// 				}
// 				if (orderBy == undefined || orderBy == '') {
// 					orderBy = 'first_name asc';
// 				}
// 				if ($usertype == 'admin') {
// 					$path = $sitePath + "/user/admin/listAllUser";
// 				} else {
// 					$path = $sitePath + "/user/user/listAllUser";
// 				}
// 				var psychologist_id = $('#filter_psychologist').val(),
// 				difficulty_id = $('#filter_difficulty').val(),
// 				group_id = $('#filter_group').val();
//         search_text = $('#search_text').val();

// 	if(rememberFilter.current_page!=-1 && rememberFilter.current_page!='' )
// 		offset=(parseInt(rememberFilter.current_page)-1)*40;

// 				$.ajax({
// 					type: 'post',
// 					url: $path,
// 					async: true,
// 					data: {
// 						"offset": offset,
// 						"orderBy": orderBy,
// 			"psychologist_id": rememberFilter.psychologist_id,
//             "difficulty_id": rememberFilter.difficulty_id,
//             "group_id": rememberFilter.group_id,
//             "search_txt": rememberFilter.search_text

// 					},
// 					success: function(response) {

// 						if ($usertype == 'Psychologist') {
// 							$('#tab1').html('');
// 							$('#tab1').html('<br/>' + response);

// 							difficulty_id = (difficulty_id>0) ? difficulty_id: 0;
// 							psychologist_id = (psychologist_id>0) ? psychologist_id: 0;
// 							group_id = (group_id>0) ? group_id: 0;

// 							$('#filter_difficulty').val(difficulty_id);
// 							$('#filter_psychologist').val(psychologist_id);
// 							$('#filter_group').val(group_id);
//       			$('#search_text').val(rememberFilter.search_text);
// 			//$("#topleaveforedit").addClass("topleaveforedit");
// 		} else {
// 			$('#content').html(response);
// 			$("#topleaveforedit").addClass("topleaveforedit");
// 		}
// 	}
// });
// 			}

function listUser(orderBy, offset) {
	
	if (offset == undefined || offset == 0 || !offset) {
		offset = 0;
	}
	if (orderBy == undefined || orderBy == '') {
		orderBy = 'id asc';
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
		offset=(parseInt(rememberFilter.current_page)-1)*40;
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
			if ($usertype == 'Psychologist') {
				$('#tab1').html('');
				 $('#tab1').html('<br/>' + response);
				// difficulty_id = (difficulty_id>0) ? difficulty_id: 0;
				// psychologist_id = (psychologist_id>0) ? psychologist_id: 0;
				// group_id = (group_id>0) ? group_id: 0;
				$('#filter_difficulty').val(rememberFilter.difficulty_id);
      			$('#filter_psychologist').val(rememberFilter.psychologist_id);
      			$('#filter_group').val(rememberFilter.group_id);
      			$('#search_text').val(rememberFilter.search_text);
      			//$('#filter_group').trigger('change');
				//$("#topleaveforedit").addClass("topleaveforedit");
			} else {
				$('#content').html(response);
				$("#topleaveforedit").addClass("topleaveforedit");
			}
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
			$('.tab_container').html(response);
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

				console.log(psychologist_id,difficulty_id,group_id)

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
            $('#tab1').html('');
			$('#tab1').html('<br/>' + response);
			$('#search_text').val("");
            $('#filter_difficulty').val(rememberFilter.difficulty_id);
            $('#filter_psychologist').val(rememberFilter.psychologist_id);
            $('#filter_group').val(rememberFilter.group_id);
            $('#search_text').val(rememberFilter.search_text);

		}

	});
			}
var groupid=0;
$(function(){
	groupid=$('#group').val();
})
			function getSelectedGrpId(grpids,sel_psy,initial){
				//debugger;
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
			if(initial !=false){
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
					// if ($usertype == 'Psychologist')
					// 	$('#tab1').html(response);
					// else
					// 	$('#content').html(response);
					// $('#group').val(selected_grp_id);



					$('#psychologist').html('');
                    response=JSON.parse(response);
                    $('#psychologist').append("<option value='0'>Välj psykolog</option>")
                    var arrids=[];
                    response.forEach(function(x){
                        if(arrids.indexOf(x.id)==-1){
                        arrids.push(x.id);
                            $('#psychologist').append("<option  value='"+x.id+"'>"+x.first_name+" "+x.last_name+"</option>")
                        }
                    })
                    //debugger;
                    if(sel_psy){
                    $('#psychologist').val(sel_psy);
                }

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
			$('.tab_container').html(response);
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
				$("#error_email").html('<label class="error">Anv&#228;ndaren finns redan.</label>');
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
			//difficulty:{required:true}	,
			//psychologist:{required:true},
			from: {
				required: true
			},
			to: {
				required: true
			}
			//confirmPassword: {equalTo: "#password"}  ,


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

			/*if ($contact_number.length > 0)
			if(!isValidContactNumber($contact_number))
			$('#contact_number').val('');
		*/
		if ($email.length > 0)
			if (!isValidEmailAddress($email))
				$('#email').val('');


			var str = $("#frmAddUser").serialize();
			// console.log(JSON.stringify(str));return false;
	str+="&psychologist_id="+rememberFilter.psychologist_id;
	str+="&difficulty_id="+rememberFilter.difficulty_id;
	str+="&group_id="+rememberFilter.group_id;
	str+="&search_txt="+rememberFilter.search_text;
	if(rememberFilter.current_page!=-1 && rememberFilter.current_page!='' )
	str+="&offset="+parseInt(((rememberFilter.current_page)-1)*40);
			if ($usertype == 'admin') {
				$path = $sitePath + "/user/admin/addUser";
			} else {
				$path = $sitePath + "/user/user/addUser";
			}

			// if($frmAddUser!= str){
				$.ajax({
					type: "Post",
					url: $path,
					data: str,
					async: false,
					beforeSend: function() {
						checkUsername($("#username").val());
			//$('#myDiv').length ;

			if ((!$("#frmAddUser").valid()) || ($("#checkEmail").val() == "")) {
				$("#preLoader").hide();
				return false;
			}

			//if($('input[name=tgPoint[]]').length<1){$.facebox($jsLang['enter_atleast_one_min_cost']); return false;}
		},
		success: function(response) {
			if (response) {
				$.facebox($jsLang['user_add_success']);
				$('#facebox').delay($fadeOutTime).fadeOut();
				$('#facebox_overlay').delay(500).fadeOut();

				// if ($usertype == 'Psychologist') {
				// 	$('#tab1').html('');
				// 	$('#tab1').html('<br/>' + response);
				// } else
				// $('#content').html(response);

						$('#filter_difficulty').val(rememberFilter.difficulty_id);
		      			$('#filter_psychologist').val(rememberFilter.psychologist_id);
		      			$('#filter_group').val(rememberFilter.group_id);
		      			$('#search_text').val(rememberFilter.search_text);

		      			listUser('id asc','');

			}

		}
	});
			/*}else{
			$.facebox('Nothing changed');
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();

			listUser('id asc','');
		}*/
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
						$('#tab1').html('<br/>' + response);
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
