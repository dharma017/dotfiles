<?php
$usertype = getUserType();
$patientId = $this->session->userdata("p_id");
$userId = $this->session->userdata("user_id");
/**
TO STOP OTHER TO VIEW DETAIL USING WORKSHEET ID
*/
//echo "$rows->user_id || $userId"; die();
/*if ($usertype == 'Psychologist') {
    if ($rows->user_id != $patientId)
        redirect(site_url("worksheet"));
}
else {
    if ($rows->user_id != $userId)
        redirect(site_url("worksheet"));
}*/
?>
<?php
$preview = $this->input->get("preview");
 // echo $preview;
?>
<div  class="heading col1">
    <?php
    $usertype = getUserType();
    //$result=$this->getPageContent(1);
    ?>
    <?php if ($view_mode != "stage") { ?>
    <h1 class="mainsubsheading"><?php echo $result->page_title ? $result->page_title : lang('worksheet'); ?></h1>
    <?php } ?>
</div>
<div id ="contentArea" class="pad10  clear templateWorksheet">
    <div class="<?php echo ($view_mode != "stage") ? 'usercontent' : 'stagedStep' ?>" style="<?php echo ($view_mode != "stage") ? '' : 'margin-left: -10px' ?>">
        <p> <?php echo nl2br(stripslashes($result->content)); ?> </p>
        <div class="curvesection clear redigerasection">
            <div class="curveTop"></div>
            <div class="curveMiddle">
                <div class="innersections" style="padding-top:0;">
                    <?php
                    if ($rows->user_id == $userId):
                        ?>
                    <div class="row" style="padding-bottom:10px">
                          <?php if($preview!='preview'):
                                if($rows->templateId!=22 && $rows->templateId!=23): //dont show Edit button for tics type worksheet
                            ?>
                            <a href="#" class="btnSmallall" id="editMenu" onclick="makeEditable()"><?= lang('txt_edit') ?> <img src="<?php echo base_url() ?>images/admin_icons/edit.png" /></a>
                        	<?php
                                endif;
                            endif ?>
                        <input type="button" value="<?= lang('save') ?>"  id="update_worksheet_top" class="btnsikall" style="">
                    </div>
                <?php endif; ?>
                <div class="clear"></div>
                <?php
                $this->load->view("worksheet/worksheet_form_detail");
                $this->load->view("worksheet/worksheet_comments");
                if ($archive_data):
                    $this->load->view("worksheet/worksheet_history");
                endif;
                ?>
            </div>
            <!-- end of intersection -->
            </div>
            <!-- end of middle curve -->
            <div class="curveBottom "></div>
        </div>
            <div class="wrapperNew">
            <div id="footer" class="clear row">
        <?php if ($usertype == 'Psychologist'): ?>
                <?php if ($next_id): ?>
                    <a style="float:left;" href="<?php echo site_url("worksheet/viewFormData/".$next_id)?>" class="btnMidall"><?= lang('previous') ?></a>
                <?php else: ?>
                    <a style="float:left;" href="<?php echo site_url("worksheet")?>" class="btnMidall"><?= lang('back') ?></a>
                <?php endif ?>
                <?php if ($prev_id): ?>
                    <a style="float:right;" href="<?php echo site_url("worksheet/viewFormData/".$prev_id)?>" class="btnMidall"><?= lang('next') ?></a> &nbsp;&nbsp;&nbsp;&nbsp;
                <?php endif ?>
            <?php else: ?>
                <?php if ($view_mode != "stage"): ?>
                    <a href="<?php echo site_url("worksheet"); ?>" class="btnMidall"> <?= lang('back') ?></a>
                <?php endif ?>
            <?php endif ?>
        </div>
        </div>
    </div>
    <?php echo ($view_mode == "stage") ? '</div>' : ' ' ?>
    <script language="javascript" type="text/javascript">

        $("document").ready(function(){

        		$(".boxed").draggable("disable");
            $(".link_head").addClass("link_head disabled");
            $(".inputs").attr("readonly","readonly");
				    //$("li a").removeClass("link_head");
				    $("#update_worksheet").hide();
				    $('#update_worksheet_top').hide();
				    $('#update_worksheet_top').click(function(){
				        $('#update_worksheet').click();
				    })
				});

        $('.link_head').click(function() {
            if( $('#form_editable').val() == "1") {
                var count = $(this).attr("title");
                $('#li_'+count+' a').removeClass("active");
                $(this).addClass("active");
                var values = $(this).attr("id");
                $('#fld_data_'+count).val(values);
            }
        });
        function showDeleteMenu(id,todo)
        {
            if(todo=="show")
                $("#delete_link_"+id).show();
            else
                $("#delete_link_"+id).hide();
        }
        function deleteComment(commentId)
        {
            if(confirm($jsLang['alert_del_comment']))
            {
                $.ajax ({
                    type:"post",
                    url:$sitePath+"/worksheet/deleteComment",
                    data:{"commentId":commentId},
                    async:false,
                    success :function(response)
                    {
                        $.facebox($jsLang['del_comment_success']);
                        $('#facebox').delay($fadeOutTime).fadeOut();
                        $('#facebox_overlay').remove();
                        $('#container_'+commentId).remove();
                    }
                });
            }
            else
                return false;
        }
        $("#send_comment").click(function() {
            if($('#comment').val()== ""){
                alert("<?= lang('no_comment') ?>");
                return false;
            }
            var str =  $("#frm_comments").serialize();
            $.ajax ({
                type:"post",
                url:$sitePath+"/worksheet/submitComment",
                data:str,
                dataType:'json',
                async:false,
                success:function(response){
                  if (response.success) {
                    $.facebox($jsLang['comment_post_success']);
                    $('#facebox').delay($fadeOutTime).fadeOut();
                    $('#facebox_overlay').remove();
                    $("#container_comment").append(response.success);
                    $("#worksheet_count").val('');
                    $("#comment").val('');
                    if($("#up_arrow").attr("class")=="up_blue")
                    {
                        $("#up_arrow").removeClass("up_blue");
                        $("#up_arrow").addClass("up_green");
                    }
                  }else{
                      alert(response);return false;
                  }
                }
            });
        });
        $("#update_worksheet").click(function() {
            var str =  $("#frmBip").serialize();
            $.ajax ({
                type:"post",
                url:$sitePath+"/stage/emailForm",
                data:str,
                dataType:'json',
                async:false,
                success:function(response){

                    $('#form_editable').val("0");
                    $(".link_head").addClass("disabled");
                    $(".inputs").attr("readonly","readonly");
                    $("a").removeClass("link_head");
                    $("#update_worksheet").hide();
                    $("#update_worksheet_top").hide();
                    $('#editMenu').show();
                    $("#choose_ladders").hide();

                    $(".boxed").draggable("disable");

                    console.log(response);
                    if(response){
                    	$(".inputs").attr("readonly","readonly");

                    	if (response.status && response.status=='invalidate') {
	                    		alert(response.message);return false;
                    	}else{
                    		$.facebox($jsLang['worksheet_updated']);
                    	}
                		$('#facebox').delay($fadeOutTime).fadeOut();
                        $('#facebox_overlay').remove();

                        <?php if ($view_mode != "stage") : ?>
                        if(response.formDataId){
                            path = $sitePath+"/worksheet/viewFormData/"+response.formDataId;
                            window.location  = path ;
                        }
                    	<?php endif; ?>

					}
					else{
					    $.facebox($jsLang['nothing_updated']);
					    $('#facebox').delay($fadeOutTime).fadeOut();
					    $('#facebox_overlay').remove();
					}
				},
				error:function(response){
				}
			});
		});

    /**
    *function to show the detail when clicked on history item
    *@since 2012-06-20
    */
    $(".history_detail").click(function(e) {
        if($(this).closest('tr').attr("class")=="active"){
            $('table.gridtable tr td a.history_detail').html($jsLang['show_version']);
            $("table.gridtable tr").removeClass("active");
            $(".history_detail_container").slideUp('slow');
        }
        else {
            $('table.gridtable tr td a.history_detail').html($jsLang['show_version']);
            $("table.gridtable tr").removeClass("active");
            $(this).closest('tr').addClass('active');
            $(this).closest('tr').find('a.history_detail').html($jsLang['close_version']);
            $(".history_detail_container").hide();
            var worksheet_id =  $(this).attr('rel');
            $.ajax ({
                type:"post",
                url:$sitePath+"/worksheet/formDataDetailAjax/"+worksheet_id,
                async:true,
                success:function(response){
                    $("#archive_info_"+worksheet_id).html(response);
                    document.getElementById("archive_info_"+worksheet_id).innerHTML = response;
    $("#history_detail_container_"+worksheet_id).show();//slideDown('slow');
    $('.history_container').find('.link_head').addClass('disabled');
    $('.history_container').find('input').addClass('disabled');
    var intialHeightofp = null;

    $(".history_container #containerFormData").find(".textPanelABC_ws").each(function(){
        var heightofP = $(this).find('p').height();
        if(heightofP > intialHeightofp){
            intialHeightofp = heightofP;
        }
        else{
            intialHeightofp = intialHeightofp;
        }
    });
    $(".history_container #containerFormData").find(".textPanelABC_ws p").css("height", intialHeightofp + "px");
			},
			error:function(response){
			    alert("some error occured");
			}
			});
        }
        e.preventDefault();
    });

$(".close_history").click(function(e){
    $('table.gridtable tr td a.history_detail').html($jsLang['show_version']);
    $(".history_detail_container").slideUp();
    e.preventDefault();
});

function makeEditable()
{
    $("#update_worksheet").show();
    $("#update_worksheet_top").show();
    $('#editMenu').hide();
    $(".link_head").removeClass("disabled");
    $(".radio").removeClass("disabled");
    $("textarea").removeClass("disabled");
    $("textarea").attr("readonly",false);
    $("#choose_ladders").show();
    $(".inputs").attr("readonly",false);
    $("li a").addClass("link_head");
    $("#update_worksheet").show();
    $('#form_editable').val("1");
    $(".boxed").draggable("enable");
    $(".checkbox_").attr("readonly",false);
    $(".checkbox_").attr("disabled",false);
}

$(".fancybox").fancybox({
    'width'     : '6',
    'height'        : '4',
    'autoScale'     : true,
    'transitionIn'      : 'elastic',
    'transitionOut'     : 'elastic',
    'type'      : 'iframe'
});

</script>
<script language="javascript" src="<?php echo base_url(); ?>assets/public/js/inputjs.js"></script>
