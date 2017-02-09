<?php
/*
Template Name: Home Page Template
*/

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

$context['title'] = $post->title;
$context['content'] = $post->content;

$context['latest_resorts'] = Timber::get_posts(array(
		'post_type'     => 'resort',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));

$context['featured_resorts'] = Timber::get_posts(array(
		'post_type'     => 'resort',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC',
		'meta_key' => 'featured',
		'meta_value' => 'yes'
	));

$context['resort_packages'] = Timber::get_posts(array(
		'post_type'     => 'resort_package',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));

$context['resorts_packages_page'] = Timber::get_posts(61);

$context['special_offers'] = Timber::get_posts(array(
		'post_type'     => 'special_offer',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));

$context['special_offers_page'] = Timber::get_posts(58);

$context['holiday_types'] = Timber::get_terms('holiday_types');

$context['holiday_types_page'] = Timber::get_posts(275);

$context['greeting'] = Timber::get_posts(array(
		'name'		=> 'your-journey-in-luxury-and-beyond',
		'post_type'     => 'page',
		'post_status'   => 'publish',
	));

$context['testimonials'] = Timber::get_posts(array(
		'post_type'     => 'testimonial',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));

$context['resorts'] = Timber::get_posts(array(
		'post_type'     => 'resort',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));

$context['resorts_page'] = Timber::get_posts(55);

$context['whychoose'] = Timber::get_posts(array(
		'name'		=> 'why-do-people-choose',
		'post_type'     => 'page',
		'post_status'   => 'publish',
	));

$context['blogs'] = Timber::get_posts(array(
		'post_type'     => 'blog',
		'post_status'   => 'publish',
		'posts_per_page' => 3,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));

$context['resorts_logo'] = Timber::get_posts(array(
		'post_type'     => 'resorts_logo',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'orderby'       => 'date',
		'order'         => 'DESC'
	));

Timber::render('home.twig', $context);
