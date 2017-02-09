<?php

/**
 * Plugin Name: App Bootstrap
 * Depends: Classes
 */

add_action('plugins_loaded',function(){

  /*new WPPlugins\WPExtend\RegisterPostType('foo-post-type',array(
      'singular_name'=>"foo-post-type",
      'hierarchical'=>true,
      'supports'=>array(
        'editor','title','custom_fields','thumbnail'
      )
    ));*/

  /*new WPPlugins\WPExtend\RegisterTaxonomy('foo-post-type',array('foo-taxonomy-1','foo-taxonomy-2'),array(
    'show_ui' => true,
      'rewrite' => array( 'slug' => 'foo-post-type' ),
      'edit item'=>__('edit mytaxonomy in English')
  ));*/

   new WPPlugins\WPExtend\RegisterPostType('testimonial',array(
      'singular_name'=>"testimonial",
      'hierarchical'=>false,
      'supports'=>array(
        'editor','title','custom_fields'
      )
    ));

 new WPPlugins\WPExtend\RegisterPostType('blog',array(
      'singular_name'=>"Blog",
      'hierarchical'=>false,
      'supports'=>array(
        'editor','title','custom_fields','thumbnail'
      )
    ));

     new WPPlugins\WPExtend\RegisterPostType('resort',array(
      'singular_name'=>"Resort",
      'hierarchical'=>false,
      'supports'=>array(
        'editor','title','custom_fields','thumbnail'
      ),'rewrite' => array( 'slug' => 'resorts' ),
    ));

     new WPPlugins\WPExtend\RegisterPostType('room',array(
      'singular_name'=>"Room",
      'hierarchical'=>false,
      'supports'=>array(
        'editor','title','custom_fields','thumbnail'
      )
    ));

     new WPPlugins\WPExtend\RegisterPostType('dining',array(
      'singular_name'=>"Dining",
      'hierarchical'=>false,
      'supports'=>array(
        'editor','title','custom_fields','thumbnail'
      )
    ));

     new WPPlugins\WPExtend\RegisterPostType('resort_package',array(
      'singular_name'=>"Resort Package",
      'hierarchical'=>false,
      'supports'=>array(
        'editor','title','custom_fields','thumbnail'
      ),'rewrite' => array( 'slug' => 'packages' ),
    ));

     new WPPlugins\WPExtend\RegisterPostType('special_offer',array(
      'singular_name'=>"Special Offer",
      'hierarchical'=>false,
      'supports'=>array(
        'editor','title','custom_fields','thumbnail'
      ),'rewrite' => array( 'slug' => 'offers' ),
    ));

      new WPPlugins\WPExtend\RegisterTaxonomy('location',array('resort'),array(
          'show_ui' => true,
          'rewrite' => array( 'slug' => 'location' )
      ));

      new WPPlugins\WPExtend\RegisterTaxonomy('star_rating',array('resort'),array(
          'show_ui' => true,
          'rewrite' => array( 'slug' => 'star_rating' )
      ));

      new WPPlugins\WPExtend\RegisterTaxonomy('resort_types',array('resort'),array(
          'show_ui' => true,
          'rewrite' => array( 'slug' => 'resort_types' )
      ));

      new WPPlugins\WPExtend\RegisterTaxonomy('holiday_types',array('resort','special_offer','resort_package'),array(
          'show_ui' => true,
          'rewrite' => array( 'slug' => 'holiday_types' )
      ));

      new WPPlugins\WPExtend\RegisterTaxonomy('meal_plan',array('resort','resort_package'),array(
          'show_ui' => true,
          'rewrite' => array( 'slug' => 'meal_plan' )
      ));

      new WPPlugins\WPExtend\RegisterTaxonomy('transfer_types',array('resort'),array(
          'show_ui' => true,
          'rewrite' => array( 'slug' => 'transfer_types' )
      ));

      new WPPlugins\WPExtend\RegisterTaxonomy('speaking_languages',array('resort'),array(
          'show_ui' => true,
          'rewrite' => array( 'slug' => 'speaking_languages' )
      ));

      new WPPlugins\WPExtend\RegisterPostType('resorts_logo',array(
      'singular_name'=>"Resorts Logo",
      'hierarchical'=>false,
      'supports'=>array(
        'title','thumbnail'
      )
    ));

      new WPPlugins\WPExtend\RegisterPostType('resorts_video',array(
      'singular_name'=>"Resorts Video",
      'hierarchical'=>false,
      'supports'=>array(
        'title','custom_fields'
      )
    ));

});