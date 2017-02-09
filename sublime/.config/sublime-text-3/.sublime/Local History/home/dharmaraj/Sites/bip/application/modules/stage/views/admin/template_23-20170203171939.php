<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/shared/colorpicker/css/colorpicker.css"/>
<script type="text/javascript" src="<?=base_url()?>assets/shared/colorpicker/js/colorpicker.js"></script>

<div class="box box-100">
  <!-- box full-width -->
  <div class="boxin">
    <div class="header">
      <h3><?php echo $templateHeading;	?> </h3>
    </div>

    <form name="frmAddStep" id="frmAddStep" method="post">
        <?php $this->load->view("stage/admin/template_common_form");?>

	 	</ul>

    <div class="clear"></div>

    <?php $this->load->view("stage/admin/template_form_footer"); ?>

  </form>

  </div>
</div>

<style type="text/css">
	.box_form{width:86%;}
	#addFields{
		margin-bottom: 10px;
	}
	.remover{margin-top: 0px;}
	.optbtn{
		float: none;
		padding-left: 0;
	}
	#bar_color{
		width: 40px;
	}
	#bar_color #selected_color{
		width: 50px;
	}
	#container_fields span{
		margin-left: 10px;
	  	font-weight: normal;
	  	font-size: 10px;
	  	color: gray;
	}
	label.error{
		display: none !important;
	}
	.rights .sud input[type="text"]{
		width: 290px;
	}

</style>
