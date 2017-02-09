function viewHtmlDetail($recordId)
{
	var x = null;
	var x= $.ajax(
			{
				type:'post',
				url:$sitePath+"/worksheet/viewHtmlDetail/",
				async: true,
				data:{"recordId":recordId},
				success: function(response)
				{
					$('#mainWrapper').html(response);

					//document.getElementById("content").innerHTML=response;
				}
			});

}
