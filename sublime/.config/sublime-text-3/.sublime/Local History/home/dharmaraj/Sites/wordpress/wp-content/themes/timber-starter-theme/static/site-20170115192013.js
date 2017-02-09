jQuery( document ).ready( function( $ ) {

	$('.top-search').on('click', function(event) {
  		event.preventDefault();
  		if ($('#choose-a-resort').val()) {
	  		$(this).closest('form').submit();
  		}else{
  			alert('Choose a resort first');return false;
  		}
  	});


  	$('#tmt-select-resort').on('change', function(event) {
  		event.preventDefault();
  		var resort_id = $(this).val();
  		autopopulate_book_resort_form(resort_id);
  	});

	$('.resortFilter select').on('change',function(){
		$('#paginationWrapper .pagination').attr('data-paged', 1);
		filterResortPosts();
	});

	$('.offerFilter select').on('change',function(){
		$('#paginationWrapper .pagination').attr('data-paged', 1);
		filterOfferPosts();
	});

	$('.packageFilter select').on('change',function(){
		$('#paginationWrapper .pagination').attr('data-paged', 1);
		filterPackagePosts();
	});

	$('.offerFilter #filter-date,.offerFilter #filter-start_date,.offerFilter #filter-ending_date')
	    .on("input change", function (e) {
	    // console.log("Date changed: ", e.target.value);
	    $('#paginationWrapper .pagination').attr('data-paged', 1);
		filterOfferPosts();
	});

	$('.packageFilter #filter-date')
	    .on("input change", function (e) {
	    // console.log("Date changed: ", e.target.value);
	    $('#paginationWrapper .pagination').attr('data-paged', 1);
		filterPackagePosts();
	});

	setPaginationActionsResort();
	setPaginationActionsOffer();
	setPaginationActionsPackage();

	returnToTop();

});

function autopopulate_book_resort_form(id) {
	  
	  var select_resort = id;

      $( '#tmt-select-room' ).attr( 'disabled', 'disabled' );
      $( '#tmt-select-meal-plan' ).attr( 'disabled', 'disabled' );

      // If default is not selected get resorts for selected resort_type
      if( select_resort != 'Select Resort' ) {
          // Send AJAX request
          data = {
              action: 'pa_add_room_types',
              pa_nonce: pa_vars.pa_nonce,
              resort: select_resort,
          };

          // Get response and populate resort select field
          $.post( ajaxurl, data, function(response) {

              if( response.room ){
                  // Disable 'Select resort' field until resort_type is selected
                  $( '#tmt-select-room' ).html('');
                  $( '#tmt-select-room' ).html( $('<option></option>').val('0').html('Select room').attr({ selected: 'selected', disabled: 'disabled'}) );

                  // Add resorts to select field options
                  $.each(response.room, function(val, text) {
                      $( '#tmt-select-room' ).append( $('<option></option>').val(val).html(text) );
                  });

                  // $('#tmt-select-room option[value="' + pa_vars.selected_room_type.ID +'"]').attr('selected','selected');

                  // Enable 'Select resort' field
                  $( '#tmt-select-room' ).removeAttr( 'disabled' );
              }

              if( response.meal_plan ){
	              $( '#tmt-select-meal-plan' ).html('');
                  // Disable 'Select resort' field until resort_type is selected
                  $( '#tmt-select-meal-plan' ).html( $('<option></option>').val('0').html('Select meal plan').attr({ selected: 'selected', disabled: 'disabled'}) );

                  // Add resorts to select field options
                  $.each(response.meal_plan, function(val, text) {
                      $( '#tmt-select-meal-plan' ).append( $('<option></option>').val(val).html(text) );
                  });

                  // $.each(pa_vars.selected_meal_plan, function(key, obj) {
                  //     $('#tmt-select-meal-plan option[value="' + obj.term_id +'"]').attr('selected','selected');
                  // });

                  // Enable 'Select resort' field
                  $( '#tmt-select-meal-plan' ).removeAttr( 'disabled' );
              }

          });
      }
}

function filterResortPosts() {

	var searchData = {};

	searchData.star_rating = $(".filter-star-rating select").val() || [];
	searchData.resort_types = $(".filter-resort-types select").val() || [];
	searchData.holiday_types = $(".filter-holiday-types select").val() || [];
	searchData.location = $(".filter-location select").val() || [];
	searchData.paged = $('#paginationWrapper .pagination').attr('data-paged');

	searchData.price_from = $(".from select").val() || [];
	searchData.price_to = $(".to select").val() || [];

	// console.log(JSON.stringify(searchData));

	jQuery.ajax({
		type: "post",
		dataType: "json",
		url: ajaxurl,
		data: {
			action: "get_filtered_resorts",
			filter: searchData,
		},
		success: function(response) {
			if (response.type == "success") {
				$('#resorts-list').html('').html(response.html);
				$('#paginationWrapper').html('').html(response.pagination);

				setPaginationActionsResort();

				setTimeout(function(){ 
					$('.item, .scroll-items .owl-carousel .item,#resorts-list .item').matchHeight(); 
				}, 500);
				
			} else {
				alert("error occured.");
			}
		}
	});
}

function setPaginationActionsResort() {
	$('.resort-paginate .ajax-pagebutton').click(function(e) {
		e.preventDefault();
		var clickedval = $(this).attr('data-paged');
		var cur_var = parseInt($('#paginationWrapper .pagination').attr('data-paged'));
		if (clickedval == 'prev') {
			$('#paginationWrapper .pagination').attr('data-paged', cur_var - 1);
		} else if (clickedval == 'next') {
			$('#paginationWrapper .pagination').attr('data-paged', cur_var + 1);
		} else {
			$('#paginationWrapper .pagination').attr('data-paged', clickedval);
		}
		filterResortPosts();
	});
}

function filterOfferPosts() {

	var searchData = {};

	searchData.resort_name = $(".filter-resort-name select").val() || [];
	// searchData.date = $('#filter-date').val();
	searchData.start_date = $('#filter-start_date').val();
	searchData.ending_date = $('#filter-ending_date').val();
	searchData.paged = $('#paginationWrapper .pagination').attr('data-paged');

	console.log(JSON.stringify(searchData));

	jQuery.ajax({
		type: "post",
		dataType: "json",
		url: ajaxurl,
		data: {
			action: "get_filtered_offers",
			filter: searchData,
		},
		success: function(response) {
			if (response.type == "success") {
				$('#resorts-list').html('').html(response.html);
				$('#paginationWrapper').html('').html(response.pagination);

				setPaginationActionsOffer();
				setTimeout(function(){ 
					$('.item, .scroll-items .owl-carousel .item,#resorts-list .item').matchHeight(); 
				}, 500);
			} else {
				alert("error occured.");
			}
		}
	});
}

function setPaginationActionsOffer() {
	$('.offer-paginate .ajax-pagebutton').click(function(e) {
		e.preventDefault();
		var clickedval = $(this).attr('data-paged');
		var cur_var = parseInt($('#paginationWrapper .pagination').attr('data-paged'));
		if (clickedval == 'prev') {
			$('#paginationWrapper .pagination').attr('data-paged', cur_var - 1);
		} else if (clickedval == 'next') {
			$('#paginationWrapper .pagination').attr('data-paged', cur_var + 1);
		} else {
			$('#paginationWrapper .pagination').attr('data-paged', clickedval);
		}
		filterOfferPosts();
		setTimeout(function(){ 
					$('.item, .scroll-items .owl-carousel .item,#resorts-list .item').matchHeight(); 
				}, 500);
	});
}

function filterPackagePosts() {

	var searchData = {};

	searchData.price_from = $(".from select").val() || [];
	searchData.price_to = $(".to select").val() || [];
	searchData.resort_name = $(".filter-resort-name select").val() || [];
	searchData.date = $('#filter-date').val();
	searchData.paged = $('#paginationWrapper .pagination').attr('data-paged');

	console.log(JSON.stringify(searchData));

	jQuery.ajax({
		type: "post",
		dataType: "json",
		url: ajaxurl,
		data: {
			action: "get_filtered_packages",
			filter: searchData,
		},
		success: function(response) {
			if (response.type == "success") {
				$('#resorts-list').html('').html(response.html);
				$('#paginationWrapper').html('').html(response.pagination);

				setPaginationActionsPackage();
				setTimeout(function(){ 
					$('.item, .scroll-items .owl-carousel .item,#resorts-list .item').matchHeight(); 
				}, 500);
			} else {
				alert("error occured.");
			}
		}
	});
}

function setPaginationActionsPackage() {
	$('.package-paginate .ajax-pagebutton').click(function(e) {
		e.preventDefault();
		var clickedval = $(this).attr('data-paged');
		var cur_var = parseInt($('#paginationWrapper .pagination').attr('data-paged'));
		if (clickedval == 'prev') {
			$('#paginationWrapper .pagination').attr('data-paged', cur_var - 1);
		} else if (clickedval == 'next') {
			$('#paginationWrapper .pagination').attr('data-paged', cur_var + 1);
		} else {
			$('#paginationWrapper .pagination').attr('data-paged', clickedval);
		}
		filterPackagePosts();
	});
}

function returnToTop() {
	$(window).scroll(function() {
    if ($(this).scrollTop() >= 50) {        // If page is scrolled more than 50px
        $('#return-to-top').fadeIn(200);    // Fade in the arrow
    } else {
        $('#return-to-top').fadeOut(200);   // Else fade out the arrow
    }
	});
	$('#return-to-top').click(function() {      // When arrow is clicked
	    $('body,html').animate({
	        scrollTop : 0                       // Scroll to top of body
	    }, 500);
	});
}