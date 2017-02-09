<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (ENVIRONMENT!='production') {
    $config['useragent'] = "CodeIgniter";
    $config['mailpath']  = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
    $config['protocol']  = "smtp";
    $config['smtp_host'] = "localhost";

    if ($_SERVER['HTTP_HOST']=='bip.local')
	    $config['smtp_port'] = "1025"; //mail hog mail catcher smtp port
    else
	    $config['smtp_port'] = "25";

    $config['mailtype']  = 'html';
    $config['charset']   = 'utf-8';
    $config['newline']   = "\r\n";
    $config['wordwrap']  = TRUE;
}else{
	$config['useragent'] = "CodeIgniter";
}
