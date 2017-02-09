<?php
/*
Template Name: Book Resort Page Template
*/
$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['title'] = $post->title;
$context['content'] = $post->content;

$context['countries'] = Tmt_Countries::items();

$context['request'] = $_REQUEST;

$id = $_REQUEST['id'];

$context['resort'] = new TimberPost($id);

$context['resort_extras']= get_resort_related_objects($id);

$errMsg = '';
$succMsg = '';

if (isset($_POST['tmt-custom-post-id'])) {
	$posted_data = (array) $_POST;

	// dd($posted_data);

	if(isset($_POST['submit']) && !empty($_POST['submit'])):
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
	        //your site secret key
	        $secret = '6LfG9CgTAAAAADrGiiduATZg2500pniI8y-xcgLC';
	        //get verify response data
	        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
	        $responseData = json_decode($verifyResponse);
	        if($responseData->success):
	            
	            //book form submission code
				$resort_post = new TimberPost($_POST['tmt-custom-post-id']);

	            $name = !empty($_POST['tmt-full-name'])? sanitize_text_field($_POST['tmt-full-name']):'';
	            $email = !empty($_POST['tmt-email'])? sanitize_email($_POST['tmt-email']):'';
	            $phone = !empty($_POST['tmt-phone-number'])? sanitize_text_field($_POST['tmt-phone-number']):'';
	            $country = !empty($_POST['tmt-select-country'])?$_POST['tmt-select-country']:'';
	            $arrival_date = !empty($_POST['tmt-arrival-date'])?$_POST['tmt-arrival-date']:'';
	            $no_nights = !empty($_POST['tmt-no-nights'])?$_POST['tmt-no-nights']:'';
	            $no_adult = !empty($_POST['tmt-no-adult'])?$_POST['tmt-no-adult']:'';
	            $no_children = !empty($_POST['tmt-no-children'])?$_POST['tmt-no-children']:'';
	            $no_infants = !empty($_POST['tmt-no-infants'])?$_POST['tmt-no-infants']:'';
	            $no_rooms = !empty($_POST['tmt-no-rooms'])?$_POST['tmt-no-rooms']:'';
	            $room = !empty($_POST['tmt-select-room'])?$_POST['tmt-select-room']:'';
	            $meal_plan = !empty($_POST['tmt-select-meal-plan'])?$_POST['tmt-select-meal-plan']:'';
	            $special_request = !empty($_POST['tmt-special-request'])? esc_textarea($_POST['tmt-special-request']):'';
	            $tenant_method = !empty($_POST['tmt-tenant-method'])?$_POST['tmt-tenant-method']:'';				
	            
	            $to = get_option( 'admin_email' );
	            $subject = 'New booking form have been submitted';
	            $htmlContent = "
	                <h1>Booking request details</h1>
	                <p><b>Resort: </b>".$resort_post->post_title."</p>
	                <p><b>Name: </b>".$name."</p>
	                <p><b>Email: </b>".$email."</p>
	                <p><b>Phone: </b>".$phone."</p>
	                <p><b>Country: </b>".$country."</p>
	                <p><b>Arrival Date: </b>".$arrival_date."</p>
	                <p><b>Number of nights: </b>".$no_nights."</p>
	                <p><b>Number of adults: </b>".$no_adult."</p>
	                <p><b>Number of children: </b>".$no_children."</p>
	                <p><b>Number of infants: </b>".$no_infants."</p>
	                <p><b>Number of rooms: </b>".$no_rooms."</p>
	                <p><b>Room: </b>".$room."</p>
	                <p><b>Meal Plan: </b>".$meal_plan."</p>
	                <p><b>Tenant Method: </b>".$tenant_method."</p>

	                <p><b>Special Request: </b>".$special_request."</p>
	            ";
	            // Always set content-type when sending HTML email
	            $headers = "MIME-Version: 1.0" . "\r\n";
	            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	            // More headers
	            $headers .= 'From:'.$name.' <'.$email.'>' . "\r\n";
	            //send email
	            @wp_mail($to,$subject,$htmlContent,$headers);
	            
	            $succMsg = 'Your booking request have submitted successfully.';
	        else:
	            $errMsg = 'Robot verification failed, please try again.';
	        endif;
	    else:
	        $errMsg = 'Please click on the reCAPTCHA box.';
	    endif;
	else:
	    $errMsg = '';
	    $succMsg = '';
	endif;	
}

$context['succMsg'] = $succMsg;
$context['errMsg'] = $errMsg;

$_args ='post_type=resort&post_status=publish&numberposts=3&orderby=rand';
$context['resorts'] = Timber::get_posts($_args);

$context['resort_list'] = Timber::get_posts(array(
    'post_type' => 'resort',
    'post_status'   => 'publish',
    'posts_per_page' => -1,
    'orderby'       => 'date',
	'order'         => 'DESC',
));

Timber::render( array( 'book-resort.twig' ), $context );