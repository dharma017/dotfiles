<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */
$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;
//echo "<pre>"; print_r($post); echo "</pre>"; exit(); 
$context['resort_types'] = Timber::get_terms('resort_types');

$context['resort_packages'] = Timber::get_posts(array(
		'post_type'     => 'resort_package',
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

$context['holiday_types'] = Timber::get_terms('holiday_types');
$context['room_types'] = Timber::get_posts(array(
		'post_type'     => 'room',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));

$context['resort'] = new TimberPost($post->resort_cf);

$rand_args ='post_type=special_offer&post_status=publish&numberposts=3&orderby=rand';
$context['special_offers_random'] = Timber::get_posts($rand_args);


if ( post_password_required( $post->ID ) ) {
    Timber::render( 'single-password.twig', $context );
} else {
    Timber::render( array( 'special_offer-detail' . '.twig' ), $context );
}
