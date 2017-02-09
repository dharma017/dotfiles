/*
function viewSteps(stageId,stepId)
{
	var x = null;
	var x= $.ajax(
			{
				type:'post',
				url:$sitePath+"/stage/viewSteps",
				async: true,
				data:{"stageId":stageId,"stepId":stepId},
				success: function(response)
				{
					$('#mainWrapper').html(response);

					//document.getElementById("content").innerHTML=response;
				}
			});

}


*/
function downloadFile(file,name)
{

    $.ajax({
        type:"post",
        url:$sitePath+"/stage/downloadFile",
        async: true,
        data:{
            "file":file,
            "name":name
        },
        success: function(response)
        {
            $('#content').html(response);

        }
    });
}


function navigateSteps(stageId, stepId)
{
    window.location.href = $sitePath+"/stage/startStep/"+stageId+"/"+stepId;
}


function summaryPage(stageId)
{
    window.location.href = $sitePath+"/stage/summaryPage/"+stageId;
}

function emailForm(NoEmail,redirect_link)
{
     warning = false;
    if(($("#templateId").val()=="5") && ($("#answerAll").val()=="1"))
    {
        var emptyFields = false;
        var numOption = $("#numOption").val();
        var i;

        for(i=0;i<=numOption; i++)
        {
            if($("#fld_data_"+i).val()=="")
                emptyFields = true;
        }
        if(emptyFields>0) {
            alert("Besvara alla frågor innan du går vidare. ");
            return false;
        }
    }
    var str = $("#frmBip").serialize();
    $.ajax({
        type	:'post',
        url	:$sitePath+"/stage/emailForm/"+NoEmail,
        async	:false,
        dataType : 'json',
        data	:str,
        success :function(response)
        {
        	if (response.status && response.status=='invalidate') {
        		alert(response.message);return false;
        	}else{
        		if(NoEmail=="1"){
        			console.log(response);
			window.location.href = redirect_link;
	            }
	            else
	            {
	                $('#contentArea').html("<p> &nbsp; </p>Uppgiften har skickats till din psykolog. ");
	                $('#next').attr('onclick', '');
	                $('#next').attr('href', redirect_link);
	                $('#last').attr('onclick', '');
	                $('#last').attr('href', redirect_link);
	            }
        	}
        },
        beforeSend	:function(){
        },
        error	:function(error)
        {
            console.log(error);
        }
    });
    return false;
}

function sendToPshychologist(stageId)
{
    url = $sitePath+"/stage/sendToPshychologist/"+stageId;
    window.location.href = url;

/*
	$.ajax({
		type:"post",
		url:$sitePath+"/stage/sendToPshychologist/",
		async:false,
		data:{"stageId":stageId},
				success: function(response)
				{
					window.location.href = response;
				}
	});
	*/
}


function completeStage(stageId)
{
    $.ajax({
        type:"post",
        url:$sitePath+"/stage/completeStage/"+stageId,
        async:true,
        data:{
            "stageId":stageId
        },
        success: function(response)
        {
            window.location.href = $sitePath+"/stage/";
        }
    });
}


function show_welcome_message(message)
{
    //alert(message);
    $.facebox(message);
    $('#facebox').delay($fadeOutTime).fadeOut();
    $('#facebox_overlay').delay(500).fadeOut();
}

/*
 * step section  end Here
 */
 (function($) {
        $.fn.extend({
            //Let the user resize the canvas to the size he/she wants
            resizeCanvas:  function(w, h) {
                var c = $(this)[0]
                c.width = w;
                c.height = h
            }
        })
    })(jQuery)


