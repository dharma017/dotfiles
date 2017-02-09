<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/admin/uploadify/uploadify.css"/>
<script src="<?php echo base_url()?>assets/admin/uploadify/jquery.uploadify.js" type="text/javascript"></script>

<div class="box box-100"> 
	<!-- box full-width -->
	<div class="boxin">
		<div class="header">
			<h3><?php echo $templateHeading;	?> </h3>
		</div>
		<form name="frmAddStep" id="frmAddStep" method="post">
			<?php  $this->load->view("stage/admin/template_common_form");?>
			<?php $this->load->view("stage/admin/template_form_footer");?>
			
			</ul>
			
		</form>
	</div>
</div>
<script>
	$(".fancybox").fancybox({
		//'href' 			: '',	
		'width'				: 630,
		'height'				: '99%',
		'autoScale'			: false,
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic',
		'type'				: 'iframe'
	});

</script>