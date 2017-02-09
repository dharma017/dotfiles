<?php
$user_id = $this->session->userdata("p_id");
$usertype = getUserType();

if ($usertype == 'Psychologist') {
    $psychologistid = ($usertype == 'Psychologist') ? $this->session->userdata('user_id') : '';
    $datalimit = DATALIMITMAX;
    $offset = (isset($_POST['offset'])) ? $_POST['offset'] : 0;
    $allUser = $this->user_model->getAllUser($offset, $datalimit, 'id asc', $psychologistid);
    $userlist = $allUser[0];
    ?>
    <div class="col2 userdiv" style="padding:0 0 0 35px;">
        V&#228;lj anv&#228;ndare:
        <select id="user_acd_psy" name="user_acd_psy" onchange="getstageforuser(this.value)" >
            <?php
            foreach ($userlist as $listdata) {
                if ($this->session->userdata("p_id") == $listdata->id)
                    $doSelect = "selected"; else
                    $doSelect = "";
                ?>
                <option value="<?php echo $listdata->id; ?>" <?php echo $doSelect; ?>><?php echo $listdata->first_name . " " . $listdata->last_name; ?></option>
    <?php } ?>
        </select>
    </div>
<?php } ?>
<?php if ($this->session->userdata('popup_once')<1): ?>
    <?php echo $this->load->view('stage/initial_notification');?>
<?php endif ?>

<div  class="heading col1">
    <?php
    $result = $this->stage_model->getPageContent(1, $this->session->userdata('difficulty_id'));
    ?>
    <h1 class="mainsubsheading"><?php echo $result->page_title ? $result->page_title : lang('start'); ?></h1>
    <?php
    if ($usertype == 'Psychologist') {
    }
    ?>
</div>
<div id ="contentArea" class="pad10 row clear">
    <div class="usercontent">
        <p>
<?php echo nl2br(stripslashes(html_entity_decode($result->content))); ?>
        </p>
    </div>
    <?php
    if($usertype=="user"){
    ?>
	    <?php if (TICS_FEATURE): ?>
            <!--Below code is commented as per client request, he wanted to remove this from start page, this option is already there in slide -->
		   <!--  <div>
		        <ul class="tics-menu">
		          <li class="my-btn white"><?php echo lang("txt_tics_manage")?> <i></i>
		             <ul class="dropdown">
		              <li><a class="manage-tics-level" href="<?php echo site_url()?>/minapp/manageTicLevels/1">Version 1</a></li>
		              <li><a class="manage-tics-level" href="<?php echo site_url()?>/minapp/manageTicLevels/2">Version 2</a></li>
		             </ul>
		          </li>
		        </ul>
		    </div> -->
	    <?php endif ?>
    <?php
    }
    ?>
    <input type="hidden" name="selectdata" value="" id="selectdata" />
    <div id="rightschek" >
        <!--beggning of main content here ..-->
            <?php $html = ''; ?>
        <div id="box1-tabular" class="content">
        </div>
    </div>
    <!--end of the main content -->
    <script>
        var usertype='<?php echo $usertype; ?>';
        $(document).ready(function() {
            if(usertype=='Psychologist'){
                getstageforuser($('#user_acd_psy').val());
            }
            else
            {
                getstageforloginuser('<?php echo $this->session->userdata('user_id'); ?>');
            }

             $(".manage-tics-level").fancybox({
                  ajax : {
                      type  : "POST",
                      data : "patient_id=<?=$this->session->userdata('user_id')?>"
                  }
              });
        });
        function getstageforuser(patient_id)
        {
            $("#selectdata").val(patient_id);
            $.ajax(
            {
                type:'post',
                url:$sitePath+"/stage/stage/getstageforuser",
                async: true,
                data:{"patient_id":patient_id},
                success: function(response)
                {
                    var data=response.split('|~|~|');
                    // $('#msg span.numbers').html(data[0]);
                    // if(data[0])
                    // {
                    //     $('#msg span.numbers').html(data[0]);
                    // }
                    // else
                    //     $('#msg span.numbers').html('0');
                    $('#box1-tabular').html(data[1]);
                }
            });
        }
        function getstageforloginuser(patient_id)
        {
            $("#selectdata").val(patient_id);
            $.ajax(
            {
                type:'post',
                url:$sitePath+"/stage/stage/getstageforuser",
                async: true,
                data:{"patient_id":patient_id},
                success: function(response)
                {
                    var data=response.split('|~|~|');
                    // $('#msg span.numbers').html(data[0]);
                    // if(data[0])
                    // {
                    //     $('#msg span.numbers').html(data[0]);
                    // }
                    // else
                    //     $('#msg span.numbers').html('0');
                    $('#box1-tabular').html(data[1]);
                }
            });
        }
        function getstageforalluser(order,offset)
        {
            patient_id=$("#selectdata").val();
            $.ajax(
            {
                type:'post',
                url:$sitePath+"/stage/stage/getstageforuser",
                async: true,
                data:{"patient_id":patient_id,"orderBy":order,"offset":offset},
                success: function(response)
                {
                    data    =   response.split('|');
                    $('#box1-tabular').html(data[3]);
                }
            });
        }
    </script>
