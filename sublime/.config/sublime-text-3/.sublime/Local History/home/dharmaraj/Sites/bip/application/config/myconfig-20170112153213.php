<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 |--------------------------------------------------------------------------
 | View File Locations
 |--------------------------------------------------------------------------
 | Contains variables setting where the default view file directories are located.
 | All must be defined with trailing slashes,
 */
// define('ENVIRONMENT', 'development');
// setting for swidish date
date_default_timezone_set ("Europe/Stockholm");
$config['swedishMonth'] = array("jan","feb","mar","apr","maj","jun","jul","aug","sep","okt","nov","dec");

// array for targe group for Stage
$config['arr_target_group']['1'] 		= "parents";
$config['arr_target_group']['2'] 		= "children";
$config['arr_target_group']['3'] 		= "parents_children";
$config['arr_target_group']['4'] 		= "Ungdom";
$config['arr_target_group']['5'] 		= "Ungdom_och_foralder";

// icon path for steps
$config['icon_path'] 			= "images/icons/";
$config['icons_max_file_size'] 		= "2223000000";
$config['image_extensions_allowed'] 	= array('jpg', 'jpeg', 'png', 'gif','bmp');
$config['ckeditor_path'] 			= "assets/shared/js/ckeditor";

$config['template_path'] 			= "images/templates/";
$config['arr_faq_type']['1']        = 'Treatment Issues';
$config['arr_faq_type']['2']        = 'Technical Issues';

if (ENVIRONMENT=='development') {
   if (in_array($_SERVER['HTTP_HOST'], array('localhost','192.168.1.121','192.168.1.103'))) {
        $config['uploadify_path']        = '/bip/assets/admin/uploadify/';
        $config['html5_path']        = 'assets/html5video/';
        $config['sound_file_path']        = '/bip/assets/sound_files/';
        $config['uploadify_upload_path'] = '/bip/images/uploads/';
        $config['icon_upload_path']      = '/bip/images/icons/';
        $config['root_pathmy']           = $_SERVER['DOCUMENT_ROOT'].'/';
        $config['thumb_path']            = $_SERVER['DOCUMENT_ROOT'].'/';
        $config['base_urlmy']            = 'http://'.$_SERVER['HTTP_HOST'].'/bip/';
    }else{
        $config['uploadify_path']        = '/assets/admin/uploadify/';
        $config['html5_path']        = 'assets/html5video/';
        $config['sound_file_path']        = 'assets/sound_files/';
        $config['uploadify_upload_path'] = '/images/uploads/';
        $config['icon_upload_path']      = '/images/icons/';
        $config['thumb_path']            = $_SERVER['DOCUMENT_ROOT'].'/';
        $config['base_urlmy']            = 'http://'.$_SERVER['HTTP_HOST'].'/';
        $config['root_pathmy']           = $_SERVER['DOCUMENT_ROOT'].'/';
    }

}elseif (ENVIRONMENT=='testing') {
    $config['uploadify_path']        = '/bipv4/assets/admin/uploadify/';
    $config['html5_path']        = 'assets/html5video/';
    $config['sound_file_path']        = '/bipv4/assets/sound_files/';
    $config['uploadify_upload_path'] = '/bipv4/images/uploads/';
    $config['icon_upload_path']      = '/bipv4/images/icons/';
    $config['root_pathmy']           = $_SERVER['DOCUMENT_ROOT'].'/';
    $config['thumb_path']            = $_SERVER['DOCUMENT_ROOT'].'/';
    $config['base_urlmy']            = 'http://'.$_SERVER['HTTP_HOST'].'/bipv4/';
}else{  //production
    $config['uploadify_path']        = '/assets/admin/uploadify/';
    $config['html5_path']        = 'assets/html5video/';
    $config['sound_file_path']        = 'assets/sound_files/';
    $config['uploadify_upload_path'] = '/images/uploads/';
    $config['icon_upload_path']      = '/images/icons/';
    $config['thumb_path']            = $_SERVER['DOCUMENT_ROOT'].'/';
    $config['base_urlmy']            = 'http://'.$_SERVER['HTTP_HOST'].'/';
    $config['root_pathmy']           = $_SERVER['DOCUMENT_ROOT'].'/';
}

$config["file_category"] =   array(1=>"progression" , 2=>"example", 3=>"form" , 4=>"exercises");

/* End of file myconfig.php */
/* Location: system/application/config/myconfig.php */
