<div class="box box-100"> 
  <!-- box full-width -->
  <div class="boxin">
    <div class="header">
      <h3><?php echo $templateHeading;	?></h3>
    </div>
	<?php $reference = $this->input->post("param1");?>
    <form name="frmAddStep" id="frmAddStep" method="post">
    <?php $this->load->view("stage/admin/template_common_form");?>
    
    <?php $this->load->view("stage/admin/template_form_footer");?>
    </ul> 
<div class="rights">
    <div id="goaldetaildata"></div>
    </div>
<input type="hidden" value="<?php echo $reference;?>" name="reference" />        
         </form>

  </div>
</div>
<script type="text/javascript">
$(document).ready(function($) {
 showdetalgoalforstep($("#sourceStepId").val(),<?php echo $stepId;?>);
});
</script>
