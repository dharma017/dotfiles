<?php 

Routes::map('resort-gallery', function($params){
    Routes::load('page--gallery.php', $params);
});

Routes::map('resort-gallery/:name', function($params){
    Routes::load('resort-gallery.php', $params);
});

Routes::map('holiday_types/:name', function($params){
	// $query = 'post_type=page&post_name=holiday-types';
    Routes::load('page--details-holiday-types.php', $params);
});

Routes::map('holiday_types/:name/page/:pg', function($params){
	// $query = 'post_type=page&post_name=holiday-types';
    Routes::load('page--details-holiday-types.php', $params);
});

Routes::map('blog/page/:pg', function($params){
	$limit = get_field('pagination_limit','option');
    $query = 'posts_per_page='.$limit.'&post_type=blog&paged='.$params['pg'];
    Routes::load('page--blog.php', null, $query);
});

// Routes::map('blog/page/:pg', function($params){
// 	$limit = get_field('pagination_limit','option');
//     $query = 'posts_per_page='.$limit.'&post_type=blog&paged='.$params['pg'];
//     Routes::load('page--resort.php', null, $query);
// });
Routes::map('create-your-dream-holidays', function($params){
    Routes::load('create-dream-holiday.php', $params);
});