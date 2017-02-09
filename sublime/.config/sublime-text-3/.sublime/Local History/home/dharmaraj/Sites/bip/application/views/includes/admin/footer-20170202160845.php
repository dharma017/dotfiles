</div> <!-- closing of content div -->
<?php 
$nav =  $this->uri->segment(1); 

if (!($nav=="login" || !$nav)){?>

<div id="footer">
<!-- footer -->



<!--<div class="row">
				<p align="right"  style="padding-right:10px;"><?php echo $this->lang->line("copyright_message");?></p>
			</div>-->

				<p align="right"  style="padding-right:10px;"><?php echo $this->lang->line("copyright_message");?></p>
	
            
            </div>
            	<?php  }?>
			
			</div><!-- .inner-container -->
		</div><!-- #container -->
	<div class="overlay" style=" display:none;"></div>
   <script language="javascript">
$(document).ready(function(){
$('#content').css({'margin':'28px 0 0','padding':'0px'});
		screenMidWidth = (screen.width/2)-100; // width and height calculation for loading images to center its position
		screenMidHght = (screen.height/2)-100;

	
	
	$('#content').ajaxSend(function(r,s)
		{  
	
	 $('#preLoader').addClass('centers').show();
});  
		
	$('#content').ajaxStop(function(r,s)
		{  
		//$('#content').css({'margin':'28px 0 0','padding':'0px'}); 			
		$("#preLoader").hide();			
									  
		});  
		
});
hash = (window.location.hash);
//alert (hash);
if(hash){
param = hash.split('|');

}

$.ajaxSetup({ cache: false});
</script>
</body></html>


	