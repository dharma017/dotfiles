<?php
/*
Template Name: Blog Page Template
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
    'post_type' => 'blog',
    'post_status'   => 'publish',
    'posts_per_page' => get_field('pagination_limit','option'),
    'orderby'       => 'date',
	'order'         => 'DESC',
	'paged' => $paged
);
/* THIS LINE IS CRUCIAL */
/* in order for WordPress to know what to paginate */
/* your args have to be the defualt query */
query_posts($args);
/* make sure you've got query_posts in your .php file */
$context['posts'] = Timber::get_posts();
$context['pagination'] = Timber::get_pagination();
wp_reset_query();


Timber::render('blog.twig', $context);