<?php echo current_url();?>
<?php $uploadifyPath =base_url()."assets/shared/js/uploadify/" ?>

<?php $fcpath = str_replace("\\","/",FCPATH);?>

<link href="<?php echo $uploadifyPath;?>uploadify.css" type="text/css" rel="stylesheet" />
<script src="http://code.jquery.com/jquery-1.5.min.js" type="text/javascript"></script>



<script type="text/javascript" src="<?php echo $uploadifyPath;?>jquery.uploadify.js"></script>

<script type="text/javascript">
function startUpload(id, conditional)
{
	if(conditional.value.length != 0) {
		$('#'+id).fileUploadStart();
	} else
		alert("You must enter your name. Before uploading");
}
</script>
<script type="text/javascript">


$(document).ready(function() {
	$("#fileUploadname").fileUpload({
		'uploader': '<?php echo $uploadifyPath; ?>uploader.swf',
		'cancelImg': '<?php echo $uploadifyPath; ?>cancel.png',
		'script': '<?php echo base_url();?>upload_name.php',
		'folder': 'images/icons',
		'multi': false,
		'auto':true,
		'displayData': 'percentage',
		onComplete: function (evt, queueID, fileObj, response, data) {
			alert("Successfully uploaded: ");
		}
	});

	$('#name').bind('change', function(){
		$('#fileUploadname').fileUploadSettings('scriptData','&name='+$(this).val());
	});


});

</script>
<legend></legend>
		<strong>Name:  </strong>
<input name="name" id="name" type="text" maxlength="255" size="50" />
        <br /><br />
<div id="fileUploadname">You have a problem with your javascript</div>
<a href="javascript:startUpload('fileUploadname', document.getElementById('name'))">Start Upload</a> |  <a href="javascript:$('#fileUploadname').fileUploadClearQueue()">Clear Queue</a>
<p>&nbsp;</p>
<p></p>
