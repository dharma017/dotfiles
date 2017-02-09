<?php
/*
Template Name: Gallery Page Template
*/
 global $paged;
    if (!isset($paged) || !$paged){
        $paged = 1;
    }

$context = Timber::get_context();
$post = new TimberPost();

$context['post'] = $post;

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

Timber::render('gallery.twig', $context);
