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

Timber::render('holiday-types.twig', $context);
