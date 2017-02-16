// JavaScript Document
function addIconForm()
{
	$(document)[0].title = 'BIP Admin Panel'; 
	$.ajax(
			{
				type:'post',
				url:$sitePath+"/setting/admin/addIconForm",
				async: false,
				data:{},
				success: function(response)
				{
                                    //alert($sitePath+"/setting/admin/addDifficultyForm"+response);
					$('#box-icon').html(response);
					$("#frmAddicon").validate({
							rules:{
									iconName:{required:true},
									iconFileName:{required:true}
													
								  },
							messages:{										
									iconName:{required:$jsLang['required']},
									iconFileName:{required:$jsLang['required']}
								 }
						});		
				}
			});	
}
function addIcon()
{	
$(document)[0].title = 'BIP Admin Panel'; 
	 var str = $("#frmAddicon").serialize();
		 $.ajax({
			type:"Post",
			url:$sitePath+"/setting/admin/addIcon",
			data:str,
			forceSync:true,
			async:false,
			beforeSend:function(){
				 //$('#myDiv').length ;
				 if(!$("#frmAddicon").valid()){
					 $("#preLoader").hide();
						return false;
					}
					
				//if($('input[name=tgPoint[]]').length<1){$.facebox($jsLang['enter_atleast_one_min_cost']); return false;}	
						},
			success:function(response){
			
			$.facebox($jsLang['icon_added_successfully']);	$('#facebox').delay($fadeOutTime).fadeOut();
                $('#facebox_overlay').delay(500).fadeOut();
			$('#box-icon').html(response);
				}
		});
}
function listIcon()
{
	$(document)[0].title = 'BIP Admin Panel'; 
	$.ajax(
			{
				type:'post',
				url:$sitePath+"/setting/admin/listAllIcon",
				async: true,
				success: function(response)
				{
					$('#box-icon').html(response);
				}
			});	
}

function deleteIcon(iconId)
{
	$(document)[0].title = 'BIP Admin Panel'; 
	
	$.ajax(
			{
				type:'post',
				url:$sitePath+"/setting/admin/deleteIcon",
				async: true,
				data:{"iconId":iconId},
				success: function(response)
				{
					$('#box-icon').html(response);
					$.facebox($jsLang['icon_deleted_successfully']);	$('#facebox').delay($fadeOutTime).fadeOut();
                $('#facebox_overlay').delay(500).fadeOut();
				}
			});	
}


// function for difficulty

// JavaScript Document

function addDifficultyForm()
{
    if($usertype=='admin')    {
        $path=$sitePath+"/setting/admin/addDifficultyForm"; 
        $(document)[0].title = 'BIP Admin Panel'; 
    }
    else    {
        $path=$sitePath+"/setting/setting/addDifficultyForm";; 
    }
    //
    $.ajax(
    {
        type:'post',
        url:$path,
        async: false,
        data:{},
        success: function(response)
        {
            //alert($sitePath+"/setting/admin/addDifficultyForm"+response);
            $('#box-difficulty').html(response);
            $("#frmAddDifficulty").validate({
                rules:{
                    difficultyName:{
                        required:true
                    }					
                },
                messages:{										
                    difficultyName:{
                        required:$jsLang['required']
                    }
									
                }
            });		
        }
    });	
}
function listDifficulty()
{
    if($usertype=='admin')    {
        $path=$sitePath+"/setting/admin/listAllDifficulty"; 
        $(document)[0].title = 'BIP Admin Panel'; 
    }
    else    {
        $path=$sitePath+"/setting/setting/listAllDifficulty"; 
    }
   
    $.ajax(
    {
        type:'post',
        url:$path,
        async: true,
        success: function(response)
        {
            $('#box-difficulty').html(response);
        }
    });	
}
function editDifficulty(difficultyId)
{
    if($usertype=='admin')    {
        $path=$sitePath+"/setting/admin/editDifficulty"; 
        $(document)[0].title = 'BIP Admin Panel'; 
    }
    else    {
        $path=$sitePath+"/setting/setting/editDifficulty"; 
    }
    
  
    $.ajax(
    {
        type:'post',
        url:$path,
        async: true,
        data:{
            difficultyId:difficultyId
        },
        success: function(response)
        {
            $('#box-difficulty').html(response);
            $("#frmAddDifficulty").validate({
                rules:{
                    difficultyName:{
                        required:true
                    }
									
													
                },
                messages:{										
                    difficultyName:{
                        required:$jsLang['required']
                    }
									
                }
            });		
        }
    });	
}

function addDifficulty()
{	
    //alert($usertype);
    if($usertype=='admin')    {
        $path=$sitePath+"/setting/admin/addDifficulty"; 
    }
    else    {
        $path=$sitePath+"/setting/setting/addDifficulty";
    }
         
    $(document)[0].title = 'BIP Admin Panel'; 
    var str = $("#frmAddDifficulty").serialize();
    $.ajax({
        type:"Post",
        url:$path,
        data:str,
        forceSync:true,
        async:false,
        beforeSend:function(){
            //$('#myDiv').length ;
            if(!$("#frmAddDifficulty").valid()){
                $("#preLoader").hide();
                return false;
            }
					
        //if($('input[name=tgPoint[]]').length<1){$.facebox($jsLang['enter_atleast_one_min_cost']); return false;}
        },
        success:function(response){
			
            $.facebox('Difficulty added Successfully');
            $('#facebox').delay($fadeOutTime).fadeOut();
            $('#facebox_overlay').delay(500).fadeOut();
            //$('#box-difficulty').html(response);
        }
    });
}




function deleteDifficulty(difficultyId)
{
    
    if($usertype=='admin')    {
        $path=$sitePath+"/setting/admin/deleteDifficulty"; 
    }
    else    {
        $path=$sitePath+"/setting/setting/deleteDifficulty";
    }
         
    $(document)[0].title = 'BIP Admin Panel'; 
    if(!confirm($jsLang['alert_delete_difficulty']))
    {
        return false;
    }
    $.ajax(
    {
        type:'post',
        url: $path,
        async: true,
        data:{
            "difficultyId":difficultyId
        },
        beforeSend:function(){
        //checkdifficultyinstage(difficultyId);
        },
        success: function(response)
        {
            $('#box-difficulty').html(response);
            $.facebox($jsLang['alert_del_difficulty']);
            $('#facebox').delay($fadeOutTime).fadeOut();
            $('#facebox_overlay').delay(500).fadeOut();
        }
    });	
}


function checkdifficultyinstage(difficultyId)
{
    if($usertype=='admin')    {
        $path=$sitePath+"/setting/admin/checkdifficultyinstage"; 
    }
    else    {
        $path=$sitePath+"/setting/setting/checkdifficultyinstage";
    }
    
    $.ajax({
        url: $path,
        type:'post',
        data:{
            'difficultyId':difficultyId
        },
        async: true,
        success : function(response) {
            if(response == 1){
                deleteDifficulty(difficultyId);
            }
            else
            {
                if($usertype=='admin')
                    alert("Error Deleting Difficulty. Delete the stages related to the selected group and try again.");
                else
                    alert("Det finns anv�ndare kopplade till denna grupp. Kan inte ta bort.");
					
				}
		}
				
	});	
}


function showContent(id)
{
	//alert(id);
	$.ajax({
		url:$sitePath+"/setting/admin/showContent",
		type:'post',
		data:{'id':id},
		async: true,
		success : function(response) {
			$('#pg_content').html("");
			$('#pg_content').html(response);
		}
				
	});
}


function savePageData()
{
	 var str = $("#frmAddStep").serialize();
	$.ajax({
		url:$sitePath+"/setting/admin/savePageData",
		type:'post',
		data:str,
		async: true,
		success : function(response) {
			
			
				$.facebox("Page Updated !");	$('#facebox').delay($fadeOutTime).fadeOut();
                $('#facebox_overlay').delay(500).fadeOut();
				$('#container_content').html("");
				$('#container_content').html(response);
		}
			
		
	});
	
}
function addGroupForm()
{
	
    //alert("start");
    if($usertype=='admin')    {
        $path=$sitePath+"/setting/admin/addGroupForm"; 
    }
    else    {
        $path=$sitePath+"/setting/setting/addGroupForm";; 
    }
    //$(document)[0].title = 'BIP Admin Panel'; 
    //alert(path);
    //alert("end");
    $.ajax(
    {
        type:'post',
        url:$path,
        async: true,				
        success: function(response)
        {
            //alert($sitePath+"/setting/admin/addDifficultyForm"+response);
            $('#box-group').html(response);
        /*$("#frmAddGroup").validate({
							rules:{
									groupName:{required:true}
									
													
								  },
							messages:{										
									groupName:{required:$jsLang['required']}
									
								 }
						});	
						*/
				}
			});	
			
}
function listGroup()
{
	if($usertype=='admin')
	 {
		$path=$sitePath+"/setting/admin/getAllGroup"; 
	 }
	 else
	 {
		 $path=$sitePath+"/setting/setting/getAllGroup";; 
	 }
	$(document)[0].title = 'BIP Admin Panel'; 
	$.ajax(
			{
				type:'post',
				url:$path,
				async: true,
				success: function(response)
				{
					$('#box-group').html(response);
				}
			});	
}
function addGroup(todo)
{
	if($usertype=='admin')
	 {
		$path=$sitePath+"/setting/admin/addGroup"; 
	 }
	 else
	 {
		 $path=$sitePath+"/setting/setting/addGroup";
	 }
	 
	$(document)[0].title = 'BIP Admin Panel'; 
	 var str = $("#frmAddGroup").serialize();
		 $.ajax({
			type:"Post",
			url:$path,
			data:str,
			forceSync:true,
			async:false,
			beforeSend:function(){
				 if(!$("#frmAddGroup").valid()){
					 $("#preLoader").hide();
						return false;
					}			
						},
			success:function(response){
			if(todo=="edit")
			$.facebox($jsLang['alert_update_group']);
			else
			$.facebox($jsLang['alert_add_group']);	$('#facebox').delay($fadeOutTime).fadeOut();
                $('#facebox_overlay').delay(500).fadeOut();
			$('#box-group').html(response);
				}
		});
}
function editGroup(groupid)
{
	if($usertype=='admin')
	 {
		$path=$sitePath+"/setting/admin/editGroup"; 
	 }
	 else
	 {
		 $path=$sitePath+"/setting/setting/editGroup";
	 }
	$(document)[0].title = 'BIP Admin Panel'; 
	$.ajax(
			{
				type:'post',
				url:$path,
				async: true,
				data:{groupId:groupid},
				success: function(response)
				{
					$('#box-group').html(response);
					$("#frmAddGroup").validate({
							rules:{
									groupName:{required:true}
									
													
								  },
							messages:{										
									groupName:{required:$jsLang['required']}
									
								 }
						});	
				}
			});		
}
var deleteid;
function deleteGroup(id)
{
	deleteid=id;
	if($usertype=='admin')
	 {
		$path=$sitePath+"/setting/admin/deletegroup"; 
	 }
	 else
	 {
		 $path=$sitePath+"/setting/setting/deletegroup";; 
	 }
	$(document)[0].title = 'BIP Admin Panel'; 
	$.ajax(
			{
				type:'post',
				url:$path,
				async: true,
				data:{groupId:id},
				success: function(response)
				{
					//$('#box-group').html(response);
					$("#"+deleteid).remove();
				}
			});		
}
function checkuseringroup(groupid)
{
	if($usertype=='admin')
	 {
		$path=$sitePath+"/setting/admin/checkuseringroup"; 
		msg_user_exists = "Error Deleteing : There are user linked to this group.";
	 }
	 else
	 {
		 $path=$sitePath+"/setting/setting/checkuseringroup";
		 msg_user_exists = "Det finns material kopplat till denna st�rning. Kan inte ta bort.";
	 }
	
	$.ajax({
		url:$path,
		type:'post',
		data:{'groupid':groupid},
		async: true,
		success : function(response) {
			if(response == 1){
				if(confirm($jsLang['alert_delete_group']))
					deleteGroup(groupid);}
			else
				{
				alert(msg_user_exists);
				
				}
		}
				
	});	
}

function generateXlsReport(groupid)
{

	$path=$sitePath+"/user/admin/generateXlsReport/"+groupid; 

	location.href = $path;
}
