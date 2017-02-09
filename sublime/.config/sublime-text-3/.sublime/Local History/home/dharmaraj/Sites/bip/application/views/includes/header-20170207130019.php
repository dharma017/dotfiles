<!DOCTYPE html>
<?php
$has_segment1 = $this->uri->segment(1);
$theme = 'default';
if (!empty($has_segment1) && $has_segment1!='login') {
	$theme = $this->session->userdata('skins');
}

if ($this->uri->segment(1)=='superadmin') {
	$theme = "teenager";
}
if ($this->uri->segment(1)=='psychologist') {
	$theme = "children";

}
?>
<html id="<?php echo $theme;?>" class="<?php echo $theme;?> mainTemplate">

	<head>
			<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<title>BIP</title>
			<?php if ($browser_type != "outdated"): ?>
				<!--[if lte  IE 6]>
			   <script type="text/javascript">
				 location.replace("<?php echo site_url("login/old_browser"); ?>");
			   </script>
			   <![endif]-->
			<?php endif ?>

			<link rel="icon" href="<?php echo base_url(); ?>favico-16x16.png" type="image/png">

			 <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/public/css/style.css"/>

			<script type="text/javascript" src="<?php echo base_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
						<script type="text/javascript" src="<?php echo base_url(); ?>bower_components/jquery-migrate/jquery-migrate.min.js"></script>

						<link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css">
						<script type="text/javascript" src="<?php echo base_url(); ?>bower_components/jquery-ui/jquery-ui.js"></script>

			<script type="text/javascript">

				$language_code ="<?php echo $this->session->userdata('language_code'); ?>";
				$sitePath ="<?php echo site_url(); ?>";
				$baseUrl = "<?php echo base_url(); ?>";
				$currentYear= "<?php echo date('Y'); ?>";
				$fadeOutTime = 1500;
				$usertype='<?php $usertype = $this->session->userdata("logintype"); $permission=$this->session->userdata("permission"); if($usertype == "admin")if(!empty($permission)) $usertype="Psychologist"; echo $usertype;?>'
				$patient_id="<?php echo $this->session->userdata('p_id');?>";
				$timer="<?php echo $this->session->userdata('timer');?>";
				$segmentPage="<?php echo $this->uri->segment(1); ?>";
				$currentPage=window.location.href.toString().split($sitePath+'/')[1];
				$currentUserId="<?php echo $this->session->userdata('user_id');?>";
			</script>

			<script type="text/javascript">
				$jsLang	=	new Array();
				<?php
				$curNav = $this->uri->segment(1);
				$arr = $this->session->userdata('jsLang');
				foreach ($arr as $key => $val) { ?>
							$jsLang['<?php echo $key; ?>']	=	'<?php echo $val; ?>';
				<?php } ?>
			</script>

						<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/dropdown-check-list.1.5/js/ui.dropdownchecklist-1.5-min.js"></script>

			<script>
			function myInitChecklistFunction() {
				  $("#selProblem").dropdownchecklist( { emptyText: "<i>Select problem area</i>", width: 310 } );
				  $("#selTask").dropdownchecklist( { emptyText: "<i>Select available tasks</i>", width: 310 } );
				}
			</script>

			<?php if ($this->session->userdata('bip_logged_in')) { ?>

				<script type="text/javascript">
					$(function () {
						$unsaved = false;
						log_time();
						    //setup ajax error handling
						    $.ajaxSetup({
						        error: function(jqXHR, exception) {
						            if (jqXHR.status === 0) {
						                alert('No internet connection');
						            } else if (jqXHR.status == 403) {
						            		alert("Sorry, your session has expired. Please login again to continue");
							            window.location.href ="/login/logout";

						            } else if (jqXHR.status == 404) {
						                alert('Requested page not found. [404]');
						            } else if (jqXHR.status == 500) {
						                alert('Internal Server Error [500].');
						            } else if (exception === 'parsererror') {
						                alert('Requested JSON parse failed.');
						            } else if (exception === 'timeout') {
						                alert('Time out error.');
						            } else if (exception === 'abort') {
						                alert('Ajax request aborted.');
						            } else {
						                alert('Uncaught Error.n' + jqXHR.responseText);
						                window.location.href ="/index.php/login/error_report";
						            }
						        }
						    });
					});
					function log_time() {
						setTimeout(function(){
							$.ajax({
								type: 'POST',
								url: $sitePath+"/login/trackDuration",
								async: true,
								data: {},
								success: function(response){
									log_time();
								}
							});

						}, 300000); // 5 min time spent field update to prevent loss
					}
				</script>

				<!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/js/jquery-ui.min.js"></script> -->
				<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/js/jquery.livequery.js"></script>
				<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/js/jquery.meio.mask.js"></script>
				<script type="text/javascript"  src="<?php echo base_url(); ?>assets/admin/js/user.js"></script>
				<script type="text/javascript"  src="<?php echo base_url(); ?>assets/admin/js/setting.js"></script>
				<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/js/jquery.tablesorter.min.js"></script>

				<!--<script language="javascript"   src="<?php //echo base_url(); ?>assets/admin/js/form-validator/jquery.validate.min.js"></script>-->
				<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/js/form-validator/jquery.validate.min.js"></script>
				<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
				<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/shared/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
				<!-- Face box Start Here  -->
				<!--<script language="javascript"   src="<?php echo base_url(); ?>assets/shared/js/facebox/facebox.js"></script>-->
				<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/js/facebox/facebox.js"></script>
				<link	href="<?php echo base_url(); ?>assets/shared/js/facebox/facebox.css"	rel="stylesheet" title="" type="text/css" />
				<!--  modal facebox-->
				<!--<script language="javascript"   src="<?php echo base_url(); ?>assets/shared/js/facebox/jquery.facebox.js"></script>-->
				<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/js/facebox/jquery.facebox.js"></script>
				<link	href="<?php echo base_url(); ?>assets/shared/js/facebox/jquery.facebox.css"	rel="stylesheet" title="" type="text/css" />

				<!--<script language="javascript"   src="<?php echo base_url(); ?>assets/public/js/<?php echo $this->uri->segment(1); ?>.js"></script>-->
				<script type="text/javascript" src="<?php echo base_url(); ?>assets/public/js/<?php echo $this->uri->segment(1); ?>.js"></script>
				<!-- Face box end here  Here  -->
			<?php }
			?>

			<link rel="stylesheet" href="<?php echo base_url(); ?>assets/shared/jquery.confirm/jquery.confirm.css" type="text/css" />
			<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/jquery.confirm/jquery.confirm.js"></script>
						<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300italic,400italic,300,600italic,700italic' rel='stylesheet' type='text/css'>

					<?php if($theme == 'children' || $theme == 'teenager'){?>
						<!--if new theme -->
						<link rel="stylesheet" type="text/css" href="<?php  echo base_url();?>assets/public/css/newSkin.css"/>
						<?php } ?>
						<?php if($theme == 'teenager'){?>
						<!-- if theme is teenager-->
						<link rel="stylesheet" type="text/css" href="<?php  echo base_url();?>assets/public/css/teenager.css"/>
						<?php } ?>
						<?php if($theme == 'children'){?>
						<!-- if theme is children-->
						<link rel="stylesheet" type="text/css" href="<?php  echo base_url();?>assets/public/css/children.css"/>
				<?php } ?>
	</head>
	<?php
	if (!(isset($colour)))
		// $colour = "a8d975";
		$colour = "grey";
	$view_section = "user";
	?>

	<script type="text/javascript">
		var isWorksheet = '<?php echo $isWorksheet;?>';
		var form_unsaved = false;

		$(function () {

			console.log('current template ',$('#templateId').val());

			$worksheet_form = $('#frmBip');
			worksheetDefaultForm = $worksheet_form.serialize();

			$comment_form = $('#frm_comments');
			commentDefaultForm = $comment_form.serialize();

			$('#previous').on('click',function (e) {
				e.preventDefault();

				checkIfFormChanged();
				if (form_unsaved){
					unloadPage(this);
				}
				else{
					console.log('redirecting to previous step  ',this.href);
					window.location = this.href;
				}

			});

			if (isWorksheet) {
				console.log('worksheet mode');
				$('#start,#next').on('click',function (e) {
					e.preventDefault();

					checkIfFormChanged();
					if (form_unsaved){
						unloadPage(this);
					}
					else{
						if ($(this).hasClass('emailForm')){
							console.log('worksheet with email form call ',this.href);
							emailForm('1',this.href);
						}
						else{
							console.log('redirecting to next step ',this.href);
							window.location = this.href;
						}
					}
				});

				$('#update_worksheet,#update_worksheet_top,#tic-timer-stop').on('click',function (e) {
					worksheetDefaultForm = $worksheet_form.serialize();
					checkIfFormChanged();
				});

				$('#send_comment').on('click',function (e) {
					commentDefaultForm = $comment_form.serialize();
					checkIfFormChanged();
				});

			}else{
				$('#start,#next').on('click',function(e){
					e.preventDefault();
					form_unsaved = false;

					if ($(this).hasClass('emailForm')){
						console.log('worksheet with email form call ',this.href);
						emailForm('1',this.href);
					}
					else{
						console.log('redirecting to next step ( no workhseet )  ',this.href);
						window.location = this.href;
					}

				});
			}

		});

		function checkIfFormChanged() {

			form_unsaved = false;

			if(worksheetDefaultForm !== $worksheet_form.serialize()){
				form_unsaved = true;

			}

			if (isWorksheet && commentDefaultForm !==$comment_form.serialize()) {
				form_unsaved = true;
			}

			console.log('form modified', form_unsaved);
		}

		function unloadPage(link) {

			$.confirm({
		                        'title': $jsLang['unsaved_nav_alert'],
		                        'message': '',
		                        'buttons': {
		                            'yes': {
		                                'class': 'blue',
		                                'button_name': $jsLang['yes'],
		                                'action': function() {
		                                	if ($(link).hasClass('emailForm')){
		                                		console.log('worksheet with email form call ',link.href);
						emailForm('1',link.href);
		                                	} else{
		                                		console.log('redirecting to  ',link.href);
						window.location = link.href;
		                                	}
		                                }
		                            },
		                            'no': {
		                                'class': 'gray',
		                                'button_name': $jsLang['no'],
		                                'action': function() {
		                                        $(this).dialog("close");
		                                    } // Nothing to do in this case. You can as well omit the action property.
		                            }
		                        }
		                    });
		}

	</script>

	<body class="color-<?php echo $colour;?>" >

