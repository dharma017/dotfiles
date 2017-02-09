<?php

global $params;
$context = Timber::get_context();

$the_slug = $params['name']; // "This is my custom title"
$args = array(
  'name'        => $the_slug,
  'post_type'   => 'resort',
  'post_status' => 'publish',
  'numberposts' => 1
);
$post = Timber::get_posts($args)[0];

$context['post'] = $post;

$context['title'] = $post->title;
$context['content'] = $post->content;

Timber::render('resort-gallery.twig', $context);
