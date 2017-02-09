<?php
/*
Template Name: Holiday Types Page Template
*/

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

$context['post'] = $post;
$context['title'] = $post->title;
$context['content'] = $post->content;

$context['holiday_types'] = Timber::get_terms('holiday_types');

$context['resorts'] = Timber::get_posts(array(
		'post_type'     => 'resort',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));

$context['special_offers'] = Timber::get_posts(array(
		'post_type'     => 'special_offer',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));

$context['room_types'] = Timber::get_posts(array(
		'post_type'     => 'room',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));

$the_slug = $params['name'];  // "This is my custom title"
$context['resort_term'] = new TimberTerm($the_slug);


global $paged;
if (!isset($paged) || !$paged){
    $paged = 1;
}

$args = array(
				'post_type' => 'resort',
				'post_status'   => 'publish',
				'posts_per_page' => 6,
				'orderby'       => 'date',
				'order'         => 'DESC',
				'paged' => $paged,
				'tax_query' =>  array(
					array(
						'taxonomy'=> 'holiday_types',
						'field' =>'slug',
						'terms' => array($the_slug)

						)
                	)
			 );
query_posts($args);
$context['related_resorts'] = Timber::get_posts($args);
$context['pagination'] = Timber::get_pagination();
wp_reset_query();

// generating random 3 holidays type
shuffle( $context['holiday_types'] );

$term_names = [];
foreach ( $context['holiday_types'] as $cat ){
    $term_names[] = array('name'=>$cat->name,'image'=> $cat->image,'link'=>$cat->slug);
}

$context['holiday_types_random'] = array_slice( $term_names, 0, 3 );

Timber::render('holiday_types-detail.twig', $context);
