<div class="box box-100"> 
  <!-- box full-width -->
  <div class="boxin">
    <div class="header">
      <h3><?php echo $templateHeading;	?> </h3>
    </div>
    <script language="javascript">
	$("#ladder li img").click(function(){
		//alert("clicked");	
			pos = $(this).attr("id");
			$("#rad_"+pos).removeAttr("checked"); 
			$("#rad_"+pos).attr("checked", "checked");

	});
	</script>
    
    <form name="frmAddStep" id="frmAddStep" method="post">
      <?php 
	  $this->load->view("stage/admin/template_common_form");
      /*?>
	  <ul class="adm-form">
      <li>
      <label><strong> Choose Ladder:</strong></label>
          <ul id="ladder">
         
            <li><img id="ladder1" src="<?php echo base_url()?>images/ladder-1-thumb.png" alt="ladder 1"   /><br />
              <input id="rad_ladder1" name="reference" type="radio" value="1" class="nep"  <?php if($reference=="1" || !$reference) echo 'checked="checked"';?>/>
            </li>
            <li><img id="ladder2" src="<?php echo base_url()?>images/ladder-2-thumb.png" alt="ladder 2"   /><br />
              <input id="rad_ladder2" name="reference" type="radio"  value="2"  <?php if($reference=="2") echo 'checked="checked"';?> />
            </li>
            <li><img id="ladder3" src="<?php echo base_url()?>images/ladder-3-thumb.png" alt="ladder 3"   /><br />
              <input id="rad_ladder3" name="reference" type="radio"  value="3" <?php if($reference=="3") echo 'checked="checked"';?> />
            </li>
            <li><img id="ladder4" src="<?php echo base_url()?>images/ladder-4-thumb.png" alt="ladder 4" onclick="check_radio()"   /><br />
              <input id="rad_ladder4" name="reference" type="radio"  value="4"  <?php if($reference=="4") echo 'checked="checked"';?>/>
            </li>
          </ul>
          <?php */
		 $this->load->view("stage/admin/template_form_footer");?>
          </ul>
      </li>
      </ul>
    </form>
  </div>
</div>
