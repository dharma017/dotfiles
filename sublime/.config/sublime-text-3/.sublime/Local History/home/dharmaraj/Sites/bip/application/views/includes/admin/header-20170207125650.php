<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>BIP Admin Panel</title>
<link rel="icon" href="<?php echo base_url(); ?>favico-16x16.png" type="image/png">
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/admin/css/style.css" media="screen, projection, tv">

<script type="text/javascript" src="<?php echo base_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>bower_components/jquery-migrate/jquery-migrate.min.js"></script>

<link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css">
<script type="text/javascript" src="<?php echo base_url(); ?>bower_components/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/js/jquery.tablesorter.min.js"></script>

<script>
$language_code='<?php echo $this->session->userdata('language_code');?>';
$sitePath ="<?php echo site_url ();?>";
$baseUrl = "<?php echo base_url ();?>";
$currentYear= "<?php echo date ( 'Y' ); ?>";
$fadeOutTime = 1500;
$usertype='<?php echo $this->session->userdata('logintype');?>';
$currentUserId="<?php echo $this->session->userdata('user_id');?>";
</script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/dropdown-check-list.1.5/js/ui.dropdownchecklist-1.5-min.js"></script>
<script>
function myInitChecklistFunction() {
        $("#difficultyId").dropdownchecklist( { emptyText: "<i>Select Treatment</i>", width: 300 } );
        $("#selProblem").dropdownchecklist( { emptyText: "<i>Select Problem</i>", width: 300 } );
    }
</script>

<script language="javascript">
$jsLang	=	new Array();
<?php
$curNav = $this->uri->segment(1);
$arr = $this->session->userdata('jsLang');
foreach($arr as $key=>$val)
{?>
	$jsLang['<?php echo $key;?>']	=	'<?php echo $val;?>';
<?php } ?>

function strip_tags (str, allowed_tags)
{

    var key = '', allowed = false;
    var matches = [];    var allowed_array = [];
    var allowed_tag = '';
    var i = 0;
    var k = '';
    var html = '';
    var replacer = function (search, replace, str) {
        return str.split(search).join(replace);
    };
    // Build allowes tags associative array
    if (allowed_tags) {
        allowed_array = allowed_tags.match(/([a-zA-Z0-9]+)/gi);
    }
    str += '';

    // Match tags
    matches = str.match(/(<\/?[\S][^>]*>)/gi);
    // Go through all HTML tags
    for (key in matches) {
        if (isNaN(key)) {
                // IE7 Hack
            continue;
        }

        // Save HTML tag
        html = matches[key].toString();
        // Is tag not in allowed list? Remove from str!
        allowed = false;

        // Go through all allowed tags
        for (k in allowed_array) {            // Init
            allowed_tag = allowed_array[k];
            i = -1;

            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+'>');}
            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+' ');}
            if (i != 0) { i = html.toLowerCase().indexOf('</'+allowed_tag)   ;}

            // Determine
            if (i == 0) {                allowed = true;
                break;
            }
        }
        if (!allowed) {
            str = replacer(html, "", str); // Custom replace. No regexing
        }
    }
    return str;
}
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/js/jquery.meio.mask.js"></script>
<script language="javascript"	src="<?php	echo base_url ();?>assets/admin/js/form-validator/jquery.validate.min.js"></script>

<!--  fancy box start here  -->

<script type="text/javascript" src="<?php	echo base_url ();	?>assets/shared/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?php	echo base_url ();	?>assets/shared/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<!--  fancy box end here  -->

<!-- Face box Start Here  -->
<script language="javascript"	src="<?php	echo base_url ();	?>assets/shared/js/facebox/facebox.js"></script>
<link	href="<?php	echo base_url ();	?>assets/shared/js/facebox/facebox.css"	rel="stylesheet" title="" type="text/css" />
<!--  modal facebox-->
<script language="javascript" src="<?php  echo base_url (); ?>assets/shared/js/facebox/jquery.facebox.js"></script>
<!-- Face box end here  Here  -->

<!--  nice editors  -->
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/js/editor/nicEdit.js"></script>

<!--  nice editors end here -->

<!-- block ui start here-->
<script language="javascript" src="<?php  echo base_url (); ?>assets/shared/js/jquery.blockUI.js"></script>
<!-- block ui end here-->

<script language="javascript"	src="<?php	echo base_url ();?>assets/admin/js/<?php echo  $this->uri->segment(1);?>.js"></script>
</head>
<body>
<div id="header">
  <div class="inner-container">
    <h1 id="logo">

        <a class="home" href="<?php echo base_url();?>index.php/stage/admin" title="Go to admin's homepage">

        <img src="<?php echo base_url();?>images/logo.png" width="114" height="69" alt="logo"> <span class="ir"></span>

        </a>

        </h1>
    <?php
    $bip_logged_in = $this->session->userdata('bip_logged_in');
    $permission = $this->session->userdata('permission');
			if(isset($bip_logged_in) && $bip_logged_in == true) {?>
    <div id="userbox">
      <div class="inner" style="margin:15px 0 0 15px;"> <strong style="text-transform:capitalize; color:#F05823;">Welcome ! <br/>
        <span style="color:#93C53B; font-weight:bold">
        <?php  echo $this->session->userdata('first_name').' '.$this->session->userdata('last_name');	//echo $this->session->user_data('first_name');?>
        </span></strong>
        <ul class="clearfix">
        </ul>
      </div>
      <a id="logout" href="<?php echo site_url('login/logout');?>" title="Log Out"></a> </div>
    <!-- #userbox -->
    <?php } ?>
  </div>
  <!-- .inner-container -->
</div>
<!-- #header -->

<?php
if(isset($bip_logged_in) && $bip_logged_in == true) {?>
<div id="navs">
  <ul id="navigation">

    <li><span <?php if($curNav=="stage" || !$curNav) echo 'class="active"';?>><a href="<?php echo base_url();?>index.php/stage/admin">Stage</a></span></li>
	  <li><span <?php if($curNav=="page" || !$curNav) echo 'class="active"';?>><a href="<?php echo base_url();?>index.php/page/admin">Page</a></span></li>
    <li><span <?php if($curNav=="faq" || !$curNav) echo 'class="active"';?>><a href="<?php echo base_url();?>index.php/faq/admin">FAQ</a></span></li>
    <li><span <?php if($curNav=="user" || !$curNav) echo 'class="active"';?>><a href="<?php echo base_url();?>index.php/user/admin">User</a></span></li>
    <li><span <?php if($curNav=="setting" || !$curNav) echo 'class="active"';?>><a href="<?php echo base_url();?>index.php/setting/admin">Settings</a></span></li>
    <li><span <?php if($curNav=="bass" || !$curNav) echo 'class="active"';?>><a href="<?php echo base_url();?>index.php/bass/admin">BASS</a></span></li>

    <li><span <?php if($curNav=="statistics" || !$curNav) echo 'class="active"';?>><a href="<?php echo base_url();?>index.php/statistics/admin">Statistics</a></span></li>

    <?php if($this->session->userdata('language_code')==1): ?>
      <li><span <?php if($curNav=="minapp" || !$curNav) echo 'class="active"';?>><a href="<?php echo base_url();?>index.php/minapp/admin">Min app</a></span></li>
    <?php endif;?>

    <?php if(!empty($permission)):?><li><span><a href="<?php echo base_url();?>index.php/stage">Psychologist Page</a></span></li><?php endif; ?>

    <?php if (empty($permission)):
        $activeflagSe = ($this->session->userdata('language_code')==1) ? 'class="active"': '';
        $activeflagEn = ($this->session->userdata('language_code')==2) ? 'class="active"': '';
        $activeflagNo = ($this->session->userdata('language_code')==3) ? 'class="active"': '';
     ?>
    	<li><span <?php if($curNav=="translation" || !$curNav) echo 'class="active"';?>><a href="<?php echo base_url();?>index.php/translation/admin">Translation</a></span></li>

    	<!-- <li><span <?php if($curNav=="logs" || !$curNav) echo 'class="active"';?>><a href="<?php echo base_url();?>index.php/logs/admin">Developer Tools</a></span></li> -->

        <li class="flag" style="float:right;margin-right:-10px;">
        <span <?= $activeflagSe ?> onclick="switchToLanguage('1');" style="cursor:pointer; display:inline-block; padding-top:5px;padding-left:5px;"><img width="24px" src="<?= base_url().'images/icons/se.png' ?>" alt="" /></span>
        <span <?= $activeflagEn ?> onclick="switchToLanguage('2');"style="cursor:pointer; display:inline-block; padding-top:5px;margin-left:5px;"><img width="24px" src="<?= base_url().'images/icons/gb.png' ?>" alt="" /></span>
        <span <?= $activeflagNo ?> onclick="switchToLanguage('3');"style="cursor:pointer; display:inline-block; padding-top:5px;margin-left:5px;"><img width="24px" src="<?= base_url().'images/icons/no.png' ?>" alt="" /></span>
        </li>
    <?php endif ?>

  </ul>
  <!-- Search form -->
</div>
<?php } ?>
<div id="container" class="clear">
<div class="inner-container">
<center>
  <div id="loading">
    <div id="preLoader" style=" display:none;" class="centers"><img src="<?php	echo base_url ();	?>images/loading.gif" id="loading"/></div>
  </div>
</center>
<div id="content" style="margin: 28px 0px 0px; padding: 0px;">
<script type="text/javascript">
function switchToLanguage($lang_id)
{
  window.location.href=$sitePath+"/login/switchToLanguage/"+$lang_id;
}
$(function () {
    //setup ajax error handling
    $.ajaxSetup({
        error: function (x, status, error) {
            if (x.status == 403) {
                alert("Sorry, your session has expired. Please login again to continue");
                window.location.href ="/login/logout";
            }
            else {
                alert("An error occurred: " + status + "\nError: " + error);
                // window.location.href ="/index.php/user/admin/error_report";
            }
        }
    });
});
</script>
