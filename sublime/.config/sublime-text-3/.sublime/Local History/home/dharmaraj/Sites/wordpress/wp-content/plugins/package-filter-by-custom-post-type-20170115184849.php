<?php
/*
Plugin Name: Package Filter BY Resort Custom Fields
Plugin URI: http://en.bainternet.info
Description: answer to http://wordpress.stackexchange.com/q/45436/2487
Version: 1.0
Author: Bainternet
Author URI: http://en.bainternet.info
*/

add_action( 'restrict_manage_posts', 'package_admin_posts_filter_restrict_manage_posts' );
/**
 * First create the dropdown
 * make sure to change POST_TYPE to the name of your custom post type
 * 
 * @author Ohad Raz
 * 
 * @return void
 */
function package_admin_posts_filter_restrict_manage_posts(){
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    //only add filter to post type you want
    if ('resort_package' == $type){
        //change this to the list of values you want to show
        //in 'label' => 'value' format
        $values = array();

        global $wpdb;
        $posts = $wpdb->get_col("
            SELECT DISTINCT meta_value
            FROM ". $wpdb->postmeta ."
            WHERE meta_key = 'resort_cf'
            ORDER BY meta_value
        ");
        foreach ($posts as $key => $post) {
            $values[$post] = get_post( $post )->post_title;
        }
        ?>
        <select name="ADMIN_FILTER_FIELD_VALUE">
        <option value=""><?php _e('All Resorts ', 'wose45436'); ?></option>
        <?php
            $current_v = isset($_GET['ADMIN_FILTER_FIELD_VALUE'])? $_GET['ADMIN_FILTER_FIELD_VALUE']:'';
            foreach ($values as $post_key => $post_val) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $post_key,
                        $post_key == $current_v? ' selected="selected"':'',
                        $post_val
                    );
                }
        ?>
        </select>
        <?php
    }
}


add_filter( 'parse_query', 'package_posts_filter' );
/**
 * if submitted filter by post meta
 * 
 * make sure to change META_KEY to the actual meta key
 * and POST_TYPE to the name of your custom post type
 * @author Ohad Raz
 * @param  (wp_query object) $query
 * 
 * @return Void
 */
function package_posts_filter( $query ){
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if ( 'resort_package' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '') {
        $query->query_vars['meta_key'] = 'resort_cf';
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }
}