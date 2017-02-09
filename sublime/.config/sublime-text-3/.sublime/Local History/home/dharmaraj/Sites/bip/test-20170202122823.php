if($worksheet){ ?>
<script type="text/javascript">
			jQuery(document).ready(function ($) {
				if (!$.curCSS) {
	       				$.curCSS = $.css;
	    				}
					$unsaved = false;

					$('textarea,input').bind('input propertychange', function() {
					$unsaved = true;
					console.log('user data not saved',$unsaved)
					});

					var inputs = $('input[type=button], input[type=submit]');
					$(inputs).each(function() {
						$(this).click(function(event) {
							$unsaved = false;
							console.log('user data saved',$unsaved)
						});

					});

					function unloadPage(){
					if($unsaved){
					return "You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?";
					}
					}
					window.onbeforeunload = unloadPage;
				});
			</script>
<?php }else{ ?>
<script type="text/javascript">
			jQuery(document).ready(function ($) {
					if (!$.curCSS) {
	       				$.curCSS = $.css;
	    				}
					$unsaved = false;

					$('textarea,input').bind('input propertychange', function() {
					$unsaved = true;
					console.log('user data not saved',$unsaved)
					});

					var inputs = $('input[type=button], input[type=submit],#next, #starta');
					$(inputs).each(function() {
						$(this).click(function(event) {
							$unsaved = false;
						});

					});

					function unloadPage(){
					if($unsaved){
					return "You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?";
					}
					}
					window.onbeforeunload = unloadPage;
				});
			</script>
<?php } ?>
