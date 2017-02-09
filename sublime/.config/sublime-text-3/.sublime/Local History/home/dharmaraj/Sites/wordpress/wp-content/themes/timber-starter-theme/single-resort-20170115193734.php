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
$context['resorts_special_offers'] = Timber::get_posts(array(
		'post_type'     => 'special_offer',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC',
		'meta_key' => 'resort_cf',
		'meta_value' => $post->ID
	));

$context['resort_types'] = Timber::get_terms('resort_types');

$context['resort_packages'] = Timber::get_posts(array(
		'post_type'     => 'resort_package',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC',
		'meta_key' => 'resort_cf',
		'meta_value' => $post->ID
	));

$context['resort_packages_random'] = Timber::get_posts(array(
		'post_type'     => 'resort_package',
		'post_status'   => 'publish',
		'posts_per_page' => 3,
		'orderby'       => 'rand',
		'order'         => 'DESC',
		'meta_key' => 'resort_cf',
		'meta_value' => $post->ID
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
		'order'         => 'DESC',
		'meta_key' => 'resort_cf',
		'meta_value' => $post->ID
	));

$context['dining'] = Timber::get_posts(array(
		'post_type'     => 'dining',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC',
		'meta_key' => 'resort_cf',
		'meta_value' => $post->ID
	));
//echo "<pre>"; print_r($context['dining']); echo "</pre>"; exit();
$context['featured_resorts'] = Timber::get_posts(array(
		'post_type'     => 'resort',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC',
		'meta_key' => 'featured',
		'meta_value' => 'yes'
	));

$context['resorts_video'] = Timber::get_posts(array(
		'post_type'     => 'resorts_video',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC',
		'meta_key' => 'resort_cf',
		'meta_value' => $post->ID
	));
$context['video'] = $context['resorts_video'][0]->resort_video;

$context['resorts_random'] = Timber::get_posts(array(
		'post_type'     => 'resort',
		'post_status'   => 'publish',
		'posts_per_page' => 12,
		'orderby'       => 'rand',
		'order'         => 'DESC'
	));

$context['resorts'] = Timber::get_posts(array(
		'post_type'     => 'resort',
		'post_status'   => 'publish',
		'posts_per_page' => 14,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));
//echo "<pre>"; print_r($context['resorts']); echo "</pre>"; exit();

if ( post_password_required( $post->ID ) ) {
    Timber::render( 'single-password.twig', $context );
} else {
    Timber::render( array( 'resort-detail' . '.twig' ), $context );
}
