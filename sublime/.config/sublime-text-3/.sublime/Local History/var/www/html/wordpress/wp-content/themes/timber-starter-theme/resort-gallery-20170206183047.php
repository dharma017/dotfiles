<?php
global $params;
$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

$context['title'] = $post->title;
$context['content'] = $post->content;

$the_slug = $params['name']; // "This is my custom title"
$args = array(
  'name'        => $the_slug,
  'post_type'   => 'resort',
  'post_status' => 'publish',
  'numberposts' => 1
);
$resort_post = Timber::get_posts($args)[0];

$context['resort_post'] = $resort_post;

$context['resort_title'] = $resort_post->post_title;
$context['resort_content'] = $resort_post->post_content;

Timber::render('resort-gallery.twig', $context);
