<?php
/*
Template Name: Resort Page Template
*/

global $paged;

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

if (!isset($paged) || !$paged){
    $paged = 1;
}

$context['title'] = $post->title;
$context['content'] = $post->content;

$args = array(
		'post_type'     => 'resort',
		'post_status'   => 'publish',
		'posts_per_page' => get_field('pagination_limit','option'),
		'orderby'       => 'date',
		'order'         => 'DESC',
		'paged' => $paged
	);

query_posts($args);
$context['resorts'] = Timber::get_posts($args);
$context['pagination'] = Timber::get_pagination();
wp_reset_query();

$context['star_rating'] = Timber::get_terms('star_rating');
$context['resort_types'] = Timber::get_terms('resort_types');
$context['holiday_types'] = Timber::get_terms('holiday_types');
$context['location'] = Timber::get_terms('location');

// $context['search_query'] = (isset($_GET['s'])) ? $_GET['s']: the_search_query();
Timber::render('resort.twig', $context);
