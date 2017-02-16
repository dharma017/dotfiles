// JavaScript Document
function showStages(difficultyId) {

	var x = null;
	var x = $.ajax({
		type: 'post',
		url: $sitePath + "/setting/admin/showStages",
		async: true,
		data: {
			"difficultyId": difficultyId
		},
		success: function(response) {
			$('#box-copy').html(response);
		}
	});

}

function saveAutoMessage() {
	tinyMCE.activeEditor.save();
	tinyMCE.triggerSave();
	$auto_contents_sms = $('#auto_contents_sms').val();
	$auto_contents_sms_en = $('#auto_contents_sms_en').val();
	$auto_contents_sms_no = $('#auto_contents_sms_no').val();
	$auto_contents_mail = $('#auto_contents_mail').val();
	$auto_contents_mail_en = $('#auto_contents_mail_en').val();
	$auto_contents_mail_no = $('#auto_contents_mail_no').val();
	$.ajax({
		type: 'post',
		dataType:'json',
		url: $sitePath + "/setting/admin/saveAutoMessage",
		async: false,
		data: {
			auto_contents_sms: $auto_contents_sms,
			auto_contents_mail: $auto_contents_mail,
			auto_contents_sms_en: $auto_contents_sms_en,
			auto_contents_mail_en: $auto_contents_mail_en,
			auto_contents_sms_no: $auto_contents_sms_no,
			auto_contents_mail_no: $auto_contents_mail_no
		},
		success: function(response) {
		    if (response.success) {
                $.facebox('Automated Message Saved');
                $('#facebox').delay($fadeOutTime).fadeOut();
                $('#facebox_overlay').delay(1000).fadeOut();
            }else{
                alert(response);return false;
            }
		}
	});
}

function saveSMSMessage() {
	$sms_contents = $('#sms_contents').val();
	$.ajax({
		type: 'post',
		url: $sitePath + "/setting/admin/saveSMSMessage",
		async: false,
		data: {
			sms_contents: $sms_contents
		},
		success: function(response) {
			console.log(response);
		}
	});
}

function addIconForm() {
	$(document)[0].title = 'BIP Admin Panel';
	$.ajax({
		type: 'post',
		url: $sitePath + "/setting/admin/addIconForm",
		async: false,
		data: {},
		success: function(response) {
			$('#box-icon').html(response);
			$("#frmAddicon").validate({
				rules: {
					iconName: {
						required: true
					},
					iconFileName: {
						required: true
					}

				},
				messages: {
					iconName: {
						required: $jsLang['required']
					},
					iconFileName: {
						required: $jsLang['required']
					}
				}
			});
		}
	});
}

function addIcon() {
	$(document)[0].title = 'BIP Admin Panel';
	var str = $("#frmAddicon").serialize();
	$.ajax({
		type: "Post",
		url: $sitePath + "/setting/admin/addIcon",
		data: str,
		forceSync: true,
		async: false,
		beforeSend: function() {
			//$('#myDiv').length ;
			if (!$("#frmAddicon").valid()) {
				$("#preLoader").hide();
				return false;
			}

			//if($('input[name=tgPoint[]]').length<1){$.facebox($jsLang['enter_atleast_one_min_cost']); return false;}
		},
		success: function(response) {

			$.facebox($jsLang['icon_added_successfully']);
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();
			$('#box-icon').html(response);
		}
	});
}

function listIcon() {
	$(document)[0].title = 'BIP Admin Panel';
	$.ajax({
		type: 'post',
		url: $sitePath + "/setting/admin/listAllIcon",
		async: true,
		success: function(response) {
			$('#box-icon').html(response);
		}
	});
}

function deleteIcon(iconId) {
	$(document)[0].title = 'BIP Admin Panel';

	$.ajax({
		type: 'post',
		url: $sitePath + "/setting/admin/deleteIcon",
		async: true,
		data: {
			"iconId": iconId
		},
		success: function(response) {
			$('#box-icon').html(response);
			$.facebox($jsLang['icon_deleted_successfully']);
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();
		}
	});
}


// function for difficulty

// JavaScript Document

function addDifficultyForm() {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/addDifficultyForm";
		$(document)[0].title = 'BIP Admin Panel';
	} else {
		$path = $sitePath + "/setting/setting/addDifficultyForm";;
	}
	//
	$.ajax({
		type: 'post',
		url: $path,
		async: false,
		data: {},
		success: function(response) {
			$('#box-difficulty').html(response);
			$("#frmAddDifficulty").validate({
				rules: {
					difficultyName: {
						required: true
					},
					'tag[]': {
						required: true
					},
				},
				messages: {
					difficultyName: {
						required: $jsLang['required']
					},
					'tag[]': {
						required: $jsLang['required']
					},

				}
			});
		}
	});
}

function copyOthersStage($difficultyId, $stageId) {
	$.ajax({
		type: 'post',
		url: $sitePath + "/stage/admin/copyOthersStage",
		async: true,
		data: {
			"stageId": $stageId,
			"difficultyId": $difficultyId,
		},

		success: function(response) {
			$('#box-copy').html(response);
			$("#frmCopyStep").validate({
				rules: {
					stageId: {
						required: true
					}
				},
				messages: {
					stageId: {
						required: $jsLang['required']
					}
				}
			})
		}
	});
}

function copyOthersStageSave() {
	var str = $("#frmCopyStep").serialize();
	$.ajax({
		type: 'post',
		async: false,
		url: $sitePath + "/stage/admin/copyOthersStageSave",
		data: str,
		beforeSend: function() {
			if (!$("#frmCopyStep").valid()) {
				$("#preLoader").hide();
				return false;
			}
		},
		success: function(response) {
			location.href = $sitePath + "/stage/admin";
			/*$.facebox('Success in copy Stage !');
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();
			showStages(response);            */
		}
	});

}


function copyDifficulty($difficultyId, $difficultyName) {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/copyDifficulty";
		$(document)[0].title = 'BIP Admin Panel';
	} else {
		$path = $sitePath + "/setting/setting/copyDifficulty";;
	}
	//
	$.ajax({
		type: 'post',
		url: $path,
		async: false,
		data: {
			'difficultyId': $difficultyId,
			'difficultyName': $difficultyName
		},
		success: function(response) {
			$('#box-difficulty').html(response)
			$("#frmAddDifficulty").validate({
				rules: {
					difficultyName: {
						required: true
					}
				},
				messages: {
					difficultyName: {
						required: $jsLang['required']
					}

				}
			});
		}
	});
}

function copyOthersDifficulty($difficultyId, $difficultyName) {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/copyOthersDifficulty";
		$(document)[0].title = 'BIP Admin Panel';
	} else {
		$path = $sitePath + "/setting/setting/copyOthersDifficulty";;
	}
	$.ajax({
		type: 'post',
		url: $path,
		async: false,
		data: {
			'difficultyId': $difficultyId,
			'difficultyName': $difficultyName
		},
		success: function(response) {
			$('#box-copy').html(response);
			$("#frmAddDifficulty").validate({
				rules: {
					difficultyName: {
						required: true
					}
				},
				messages: {
					difficultyName: {
						required: $jsLang['required']
					}

				}
			});
		}
	});
}

function listDifficulty() {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/listAllDifficulty";
		$(document)[0].title = 'BIP Admin Panel';
	} else {
		$path = $sitePath + "/setting/setting/listAllDifficulty";
	}

	$.ajax({
		type: 'post',
		url: $path,
		async: true,
		success: function(response) {
			$('#box-difficulty').html(response);
		}
	});
}

function listOthersDifficulty() {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/listOthersDifficulty";
		$(document)[0].title = 'BIP Admin Panel';
	} else {
		$path = $sitePath + "/setting/setting/listOthersDifficulty";
	}

	$.ajax({
		type: 'post',
		url: $path,
		async: true,
		success: function(response) {
			$('#box-copy').html(response);
		}
	});
}

function checkDifficulty(difficultyName) {
	//alert(difficultyName);
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/checkdifficultyName";
	} else {
		$path = $sitePath + "/setting/setting/checkdifficultyName";
	}
	if (difficultyName) {
		$.ajax({
			type: "POST",
			url: $path,
			data: {
				"difficultyName": difficultyName
			},
			success: function(response) {
				alert("difficulty" + response);

				console.log('response:' + response + ':');
				$("#errorDifficultyName").html("");
				if (response == "1") {
					console.log('inside');
					//$("#checkDifficultyName").val("");
					return 0;
					//$("#email").addClass("error");
					$("#errorDifficultyName").html('<label class="error">Difficulty Name already exists.</label>');
				} else {
					console.log('outside');
					//$("#checkDifficultyName").val("1");
					return 1;
					// $("#error_email").html('<span style="color:#5DA93E">Anv&#228;ndaren &#228;r inte tillg&#228;nglig !</span>');
				}
			},
			error: function(e) {
				console.log(e);
				return 0;
			}
		});
	}
	return 1;
}

function editDifficulty(difficultyId) {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/editDifficulty";
		$(document)[0].title = 'BIP Admin Panel';
	} else {
		$path = $sitePath + "/setting/setting/editDifficulty";
	}


	$.ajax({
		type: 'post',
		url: $path,
		async: true,
		data: {
			difficultyId: difficultyId
		},
		success: function(response) {
			$('#box-difficulty').html(response);
			$("#frmAddDifficulty").validate({
				rules: {
					difficultyName: {
						required: true
					},
					'tag[]': {
						required: true
					},
				},
				messages: {
					difficultyName: {
						required: $jsLang['required']
					},
					'tag[]': {
						required: $jsLang['required']
					},

				}
			});
		}
	});
}

function addDifficulty() {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/addDifficulty";
	} else {
		$path = $sitePath + "/setting/setting/addDifficulty";
	}

	$(document)[0].title = 'BIP Admin Panel';
	var str = $("#frmAddDifficulty").serialize();
	$.ajax({
		type: "Post",
		url: $path,
		data: str,
		forceSync: true,
		async: false,
		beforeSend: function() {
			if (!$("#frmAddDifficulty").valid()) {
				$("#preLoader").hide();
				return false;
			}
		},
		success: function(response) {

			$.facebox($jsLang['alert_add_difficulty']);
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();
			$('#box-difficulty').html(response);
		}
	});
}

function addOthersDifficulty() {

	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/addOthersDifficulty";
	} else {
		$path = $sitePath + "/setting/setting/addOthersDifficulty";
	}

	$.blockUI({
		message: '<h1>We are copying all stages of treatment.  Please be patient.</h1> '
	});

	$(document)[0].title = 'BIP Admin Panel';
	var str = $("#frmAddDifficulty").serialize();
	$.ajax({
		type: "Post",
		url: $path,
		data: str,
		beforeSend: function() {
				$("#preLoader").show();
		},
		success: function(response) {
			$("#preLoader").hide();
			$.unblockUI();
			$.facebox($jsLang['alert_add_difficulty']);
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();
			$('#box-copy').html(response);
		}
	});
}



function deleteDifficulty(difficultyId) {

	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/deleteDifficulty";
	} else {
		$path = $sitePath + "/setting/setting/deleteDifficulty";;
	}

	$(document)[0].title = 'BIP Admin Panel';
	if (!confirm($jsLang['alert_delete_difficulty'])) {
		return false;
	}
	$.ajax({
		type: 'post',
		url: $path,
		async: true,
		data: {
			"difficultyId": difficultyId
		},
		beforeSend: function() {
			//checkdifficultyinstage(difficultyId);
		},
		success: function(response) {
			$('#box-difficulty').html(response);
			$.facebox($jsLang['alert_del_difficulty']);
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();
		}
	});
}

function deleteDifficultyCascade(difficultyId) {

	$path = $sitePath + "/setting/admin/deleteDifficultyCascade";

	$(document)[0].title = 'BIP Admin Panel';
	if (!confirm('Är du säker på att du vill ta bort den här modulen/delen?')) {
		// $.unblockUI();
		return false;
	}
	$.blockUI({
		message: '<h1>We are processing your request.  Please be patient.</h1> '
	});
	$.ajax({
		type: 'post',
		url: $path,
		async: true,
		data: {
			"difficultyId": difficultyId
		},
		success: function(response) {
			// unblock when remote call returns
			$.unblockUI();
			$('#box-difficulty').html(response);
			$.facebox("Difficulty deleted successfully with all related data !");
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();
		}
	});
}


function checkdifficultyinstage(difficultyId) {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/checkdifficultyinstage";
	} else {
		$path = $sitePath + "/setting/setting/checkdifficultyinstage";;
	}

	$.ajax({
		url: $path,
		type: 'post',
		data: {
			'difficultyId': difficultyId
		},
		async: true,
		success: function(response) {
			if (response == 1) {
				deleteDifficulty(difficultyId);
			} else {
				if ($usertype == 'admin')
				// alert("Error Deleting Difficulty. Delete the stages related to the selected group and try again.");
					deleteDifficultyCascade(difficultyId);
				else
					alert("Det finns användare kopplade till denna grupp. Kan inte ta bort.");

			}
		}

	});
}


function showContent(id) {
	//alert(id);
	$.ajax({
		url: $sitePath + "/setting/admin/showContent",
		type: 'post',
		data: {
			'id': id
		},
		async: true,
		success: function(response) {
			$('#pg_content').html("");
			$('#pg_content').html(response);
		}

	});
}


function savePageData() {
	var str = $("#frmAddStep").serialize();
	$.ajax({
		url: $sitePath + "/setting/admin/savePageData",
		type: 'post',
		data: str,
		async: true,
		success: function(response) {


			$.facebox("Page Updated !");
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();
			$('#container_content').html("");
			$('#container_content').html(response);
		}


	});

}

function addGroupForm() {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/addGroupForm";
	} else {
		$path = $sitePath + "/setting/setting/addGroupForm";;
	}
	$.ajax({
		type: 'post',
		url: $path,
		async: true,
		success: function(response) {
			$('#box-group').html(response);
			$("#frmAddGroup").validate({
				rules: {
					groupName: {
						required: true
					}


				},
				messages: {
					groupName: {
						required: $jsLang['required']
					}

				}
			});
		}
	});

}

function listGroup() {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/getAllGroup";
	} else {
		$path = $sitePath + "/setting/setting/getAllGroup";;
	}
	$(document)[0].title = 'BIP Admin Panel';
	$.ajax({
		type: 'post',
		url: $path,
		async: true,
		success: function(response) {
			$('#box-group').html(response);
		}
	});
}

function addGroup(todo) {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/addGroup";
	} else {
		$path = $sitePath + "/setting/setting/addGroup";
	}
	$(document)[0].title = 'BIP Admin Panel';
	var str = $("#frmAddGroup").serialize();
	$.ajax({
		type: "Post",
		url: $path,
		data: str,
		forceSync: true,
		async: false,
		beforeSend: function() {
			if (!$("#frmAddGroup").valid()) {
				$("#preLoader").hide();
				return false;
			}
		},
		success: function(response) {
			if (todo == "edit")
				$.facebox($jsLang['alert_update_group']);
			else
				$.facebox($jsLang['alert_add_group']);
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();
			$('#box-group').html(response);
		}
	});
}

function editGroup(groupid) {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/editGroup";
	} else {
		$path = $sitePath + "/setting/setting/editGroup";
	}
	$(document)[0].title = 'BIP Admin Panel';
	$.ajax({
		type: 'post',
		url: $path,
		async: true,
		data: {
			groupId: groupid
		},
		success: function(response) {
			$('#box-group').html(response);
			$("#frmAddGroup").validate({
				rules: {
					groupName: {
						required: true
					}


				},
				messages: {
					groupName: {
						required: $jsLang['required']
					}

				}
			});
		}
	});
}
var deleteid;

function deleteGroup(id) {
	deleteid = id;
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/deletegroup";
	} else {
		$path = $sitePath + "/setting/setting/deletegroup";;
	}
	$(document)[0].title = 'BIP Admin Panel';
	$.ajax({
		type: 'post',
		url: $path,
		async: true,
		data: {
			groupId: id
		},
		success: function(response) {
			$("#" + deleteid).remove();
		}
	});
}

function checkuseringroup(groupid) {
	if ($usertype == 'admin') {
		$path = $sitePath + "/setting/admin/checkuseringroup";
		msg_user_exists = "Error Deleteing : There are user linked to this group.";
	} else {
		$path = $sitePath + "/setting/setting/checkuseringroup";
		msg_user_exists = "Det finns material kopplat till denna st�rning. Kan inte ta bort.";
	}

	$.ajax({
		url: $path,
		type: 'post',
		data: {
			'groupid': groupid
		},
		async: true,
		success: function(response) {
			if (response == 1) {
				if (confirm($jsLang['alert_delete_group']))
					deleteGroup(groupid);
			} else {
				alert(msg_user_exists);

			}
		}

	});
}

function generateXlsReport(groupid) {

	$path = $sitePath + "/user/admin/generateXlsReport/" + groupid;

	location.href = $path;
}

function listAccounts() {

	$path = $sitePath + "/setting/admin/listAccounts";
	$(document)[0].title = 'BIP Admin Panel';

	$.ajax({
		type: 'post',
		url: $path,
		async: true,
		success: function(response) {
			$('#box-account').html(response);
		}
	});
}

function addAccountForm() {

	$path = $sitePath + "/setting/admin/addAccountForm";
	$(document)[0].title = 'BIP Admin Panel';

	$.ajax({
		type: 'post',
		url: $path,
		async: false,
		data: {},
		success: function(response) {
			$('#box-account').html(response);
			$("#frmAddAccount").validate({
				rules: {
					first_name: {
						required: true
					},
					last_name: {
						required: true
					},
					username: {
						required: true,
						// remote: {
						// 	url: $sitePath + "/setting/admin/checkUsernameExist",
						// 	type: "post"
						// }
					},
					email: {
						required: true,
						email:true
					},
					contact_number: {
						required: true
					},
					autogeneratedpw: {
						required: true,
						minlength: 8
					}
				},
				messages: {
					first_name: {
						required: $jsLang['required']
					},
					last_name: {
						required: $jsLang['required']
					},
					username: {
						required: $jsLang['required'],
						remote: "Username already exists"
					},
					email: {
						required: $jsLang['required'],
						remote: "Please enter valid email"
					},
					contact_number: {
						required: $jsLang['required']
					},
					autogeneratedpw: {
						required: $jsLang['required']
					}
				}
			});
		}
	});
}

function addAccount() {

	$(document)[0].title = 'BIP Admin Panel';
	$path = $sitePath + "/setting/admin/addAccount";
	var hidid = $('#hidid').val();
	var str = $("#frmAddAccount").serialize();

	// if($frmAddAccount!= str){

		$.ajax({
			type: "POST",
			url: $path,
			data: str,
			beforeSend: function() {
				if (!$("#frmAddAccount").valid()) {
					$("#preLoader").hide();
					return false;
				}
			},
			success: function(response) {
				/*if (hidid) {
					$.facebox('Superadmin updated');
				} else {
					$.facebox('New superadmin added');
				}*/
				$('#facebox').delay($fadeOutTime).fadeOut();
				$('#facebox_overlay').delay(500).fadeOut();
				$('#box-account').html('').html(response);
			}
		});

	// }else{
	// 	$.facebox('Nothing changed');
	// 	$('#facebox').delay($fadeOutTime).fadeOut();
	// 	$('#facebox_overlay').delay(500).fadeOut();

	// 	listAccounts();
	// }



}

function editAccount(id) {
	$(document)[0].title = 'BIP Admin Panel';
	$path = $sitePath + "/setting/admin/editAccount";

	$.ajax({
		type: 'post',
		url: $path,
		async: false,
		data: {
			account_id: id
		},
		success: function(response) {
			$('#box-account').html(response);
			$("#frmAddAccount").validate({
				rules: {
					first_name: {
						required: true
					},
					last_name: {
						required: true
					},
					username: {
						required: true,
						// remote: {
						// 	url: $sitePath + "/setting/admin/checkUsernameExist",
						// 	data: {
						// 		'user_id': id
						// 	},
						// 	type: "post"
						// }
					},
					email: {
						required: true,
						email:true
					},
					contact_number: {
						required: true
					},
					autogeneratedpw: {
						minlength: 8
					}
				},
				messages: {
					first_name: {
						required: $jsLang['required']
					},
					last_name: {
						required: $jsLang['required']
					},
					username: {
						required: $jsLang['required'],
						remote: "Username already exists"
					},
					email: {
						required: $jsLang['required'],
						//remote: "Please enter valid email"
					},
					contact_number: {
						required: $jsLang['required']
					},
					autogeneratedpw: {
						required: $jsLang['required']
					}
				}
			});
		}
	});
}

function deleteAccount(id) {

	$(document)[0].title = 'BIP Admin Panel';
	$path = $sitePath + "/setting/admin/deleteAccount"

	if (!confirm('Are you sure want to delete superadmin ?')) {
		return false;
	}

	$.ajax({
		type: 'post',
		url: $path,
		async: true,
		data: {
			"account_id": id
		},
		beforeSend: function() {},
		success: function(response) {
			$('#box-account').html(response);
			$.facebox('Superadmin deleted successfully');
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();
		}
	});
}


function changePasswordForm() {
	$(document)[0].title = 'BIP Admin Panel';
	$.ajax({
		type: 'post',
		url: $sitePath + "/setting/admin/changePasswordForm",
		async: false,
		data: {},
		success: function(response) {
			$('#box-change_password').html(response);
			$("#frmChanngePassword").validate({
				rules: {
					old_password: {
						required: true
					},
					password: {
						required: true,
						minlength: 6
					},
					password_again: {
						equalTo: "#password"
					}
				},
				messages: {
					old_password: {
						required: $jsLang['required']
					},
					password: {
						required: $jsLang['required']
					},
					password_again: {
						required: $jsLang['required']
					}
				}
			});
		}
	});
}

function updateNewPassword() {
	$(document)[0].title = 'BIP Admin Panel';
	var str = $("#frmChanngePassword").serialize();
	$.ajax({
		type: "post",
		url: $sitePath + "/setting/admin/updateNewPassword",
		data: str,
		async: false,
		beforeSend: function() {
			if (!$("#frmChanngePassword").valid()) {
				$("#preLoader").hide();
				return false;
			}
		},
		success: function(response) {

			if (response == 'fail')
				$.facebox('old password not match');
			else
				$.facebox('password changed success');

			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(500).fadeOut();

			setTimeout(function() {
				clearForm($("#frmChanngePassword"));
			}, 1000);


		},
		error: function() {
			console.log("error");
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

function saveSystemSettings(){

	var str = $('#frmAddSystemSettings').serialize();

	if ($("#timer").val().length == 0){
  	alert('Error saving, Timer field empty.');return false;
  }

	$.ajax({
		type: 'post',
		url: $sitePath + "/statistics/admin/saveSystemSettings",
		async: false,
		data: str,
		success: function(response) {
			$.facebox('Updated Successfully');
			$('#facebox').delay($fadeOutTime).fadeOut();
			$('#facebox_overlay').delay(1000).fadeOut();
		}
	});
}
