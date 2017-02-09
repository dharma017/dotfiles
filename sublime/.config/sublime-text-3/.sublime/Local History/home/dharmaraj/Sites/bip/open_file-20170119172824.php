<?php

	
	$file_name = htmlspecialchars($_REQUEST["file_name"]);

	$ext=strtolower(substr(strrchr($file_name,'.'),1));
	
	$mime_types=array();
	$mime_types['ai']    ='application/postscript';
	$mime_types['asx']   ='video/x-ms-asf';
	$mime_types['au']    ='audio/basic';
	$mime_types['avi']   ='video/x-msvideo';
	$mime_types['bmp']   ='image/bmp';
	$mime_types['css']   ='text/css';
	$mime_types['doc']   ='application/msword';
	$mime_types['eps']   ='application/postscript';
	$mime_types['exe']   ='application/octet-stream';
	$mime_types['gif']   ='image/gif';
	$mime_types['htm']   ='text/html';
	$mime_types['html']  ='text/html';
	$mime_types['ico']   ='image/x-icon';
	$mime_types['jpe']   ='image/jpeg';
	$mime_types['jpeg']  ='image/jpeg';
	$mime_types['jpg']   ='image/jpeg';
	$mime_types['js']    ='application/x-javascript';
	$mime_types['mid']   ='audio/mid';
	$mime_types['mov']   ='video/quicktime';
	$mime_types['mp3']   ='audio/mpeg';
	$mime_types['mpeg']  ='video/mpeg';
	$mime_types['mpg']   ='video/mpeg';
	$mime_types['pdf']   ='application/pdf';
	$mime_types['pps']   ='application/vnd.ms-powerpoint';
	$mime_types['ppt']   ='application/vnd.ms-powerpoint';
	$mime_types['ps']    ='application/postscript';
	$mime_types['pub']   ='application/x-mspublisher';
	$mime_types['qt']    ='video/quicktime';
	$mime_types['rtf']   ='application/rtf';
	$mime_types['svg']   ='image/svg+xml';
	$mime_types['swf']   ='application/x-shockwave-flash';
	$mime_types['tif']   ='image/tiff';
	$mime_types['tiff']  ='image/tiff';
	$mime_types['txt']   ='text/plain';
	$mime_types['wav']   ='audio/x-wav';
	$mime_types['wmf']   ='application/x-msmetafile';
	$mime_types['xls']   ='application/vnd.ms-excel';
	$mime_types['zip']   ='application/zip';
	$mime = $mime_types[$ext];
	
	if($_REQUEST['folder']=='page')
	{
	$dir      = "images/uploads/pagedata/";	
	}
	else
	{
	$dir      = "images/uploads/download/";
	}
    if ((isset($file_name))&&(file_exists($dir.$file_name))) {
	
	header('Pragma: public'); 	// required
	header('Expires: 0');		// no cache
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($dir.$file_name)).' GMT');
	header('Cache-Control: private',false);
	header('Content-Type: '.$mime);
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.filesize($dir.$file_name));	// provide file size
	header('Connection: close');
	readfile($dir.$file_name);		// push it out
	exit();

    } else {
       echo "No file exists !";
    } //end if 


	
?> 