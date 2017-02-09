<script type='text/javascript'>
    
	$(window).load(function() {
		var totalHeight=0;
			var stageHeightWhole = $("body").outerHeight();
	    $('.stage-inner').each(function(){
			var stageHeight = $(this).height();
			stageHeight = stageHeight;
			$(this).find('> .stage-bg').css('border-top-width', (stageHeight/2)+'px');
			$(this).find('> .stage-bg').css('border-bottom-width', (stageHeight/2)+1+'px');
		});
            totalHeight+=stageHeightWhole;
      // $('.somestaticclassforprint').css({"border-top-width":(totalHeight/2)+"px","border-bottom-width":(totalHeight/2)+"px"});

      console.log("totalHeight is" + totalHeight);
	});
  </script>