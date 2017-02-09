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

<script type="text/javascript">

$(function(){

	if($(".change-rating-type").length>0){

		$(".change-rating-type").click(function(){
				if($(this).val()==1){ //overall rating, hide interval box
					$("li.rate-interval-holder").addClass("hide");
					$("li.rate-interval-title").addClass("hide");
				}else{
					$("li.rate-interval-holder").removeClass("hide");
					$("li.rate-interval-title").removeClass("hide");
				}
		});
	}

	$(".colorpicker").remove();

	if($("#field_id").length>0){
		if (document.getElementById("field_id").value>=3){
			$('#addFields').hide();
		}else{
			$('#addFields').show();
		}
	}	

	initColorPicker();
})

function initColorPicker(){
	$('.color').each(function (i) {
		$('#bar_color'+i).ColorPicker({
			color: '#ffcc00',
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#selected_color'+i).css('backgroundColor', '#' + hex);
				$('#bar_color'+i).val(hex);
			}
		});

	});
}

function addFormFields() {

	var field_id = document.getElementById("field_id").value;
		id = (field_id*1)+1,
		colorid =id -1;

		$('#addFields').show();
		if (id>=3) {
			$('#addFields').hide();
		}

		$(".opt1").append('<div class="box_form" id="row' + id + '"> <ul> <li> <label><strong>Column headline</strong></label> <input class="inputs" type="text" name="fld_label[]" value=""> </li> <li> <label><strong>Color</strong></label> <input type="text" class="inputs color" id="bar_color' + colorid + '" name="fld_name[]" style="width:130px" value="" /><span id="selected_color' + colorid + '">&nbsp;</span></li> </ul> <a href="#" onClick="removeFormField(\'#row' + id + '\'); return false;" class="remover">Remove</a> </div>');

		document.getElementById("field_id").value = id;

		initColorPicker();

}

function removeFormField(id) {

	var field_id = document.getElementById("field_id").value;
	if(field_id>1)
	{
		field_id = (field_id - 1);
		document.getElementById("field_id").value = field_id;
		$(id).remove();
		$(id).fadeOut();
		$('#addFields').show();

	}

}


</script>
