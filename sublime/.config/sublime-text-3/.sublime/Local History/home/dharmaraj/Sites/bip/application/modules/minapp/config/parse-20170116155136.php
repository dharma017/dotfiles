<?php
/**
 * Parse keys
 */
if($_SERVER["HTTP_HOST"]=="www.barninternetprojektet.se" || $_SERVER["HTTP_HOST"]=="barninternetprojektet.se"){

	/*	$config['parse_appid'] = 'yRMU9gqMRJiIM8bsnwnu3BKyiOdrBhSvKGwbnDMg';
		$config['parse_masterkey'] = 'BEYUTKSQeTv8ECKQ7lrcF1JtBPW4zMfnEaIT2lLF';
		$config['parse_restkey'] = 'oLIY0GrLHHwtc8jBUCZ1mgDhVfeN31699SYtUbGj';
		$config['parse_parseurl'] = 'https://api.parse.com/1/push';
	*/
	$config['app_name'] = 'BIP';
	$config['parse_push_url'] = 'https://parseapi.back4app.com/push';
	$config['parse_installations_url'] = 'https://parseapi.back4app.com/classes/_Installation';

	$config['parse_app_id'] = 'xijLovXRp4oCdtmSVTeYyEzDpPg4cQsOIylhJfJw';
	$config['parse_client_key'] = 'VVognXXYVIRVnhnVz6PeLAM4SY8lThDguZNGWWXO';
	$config['parse_rest_api_key'] = 'ppYghtMzryPX26lyPC7I0xNs2BI7R9eiAYDC5A36';
	$config['parse_master_key'] = 'zch658788lrQdM8xTcBJ5Gey3vFlhYytnawO7Pfq';

}else{
	$config['app_name'] = 'BipAppDev';
	$config['parse_push_url'] = 'https://parseapi.back4app.com/push';
	$config['parse_installations_url'] = 'https://parseapi.back4app.com/classes/_Installation';

	$config['parse_app_id'] = 'CEFrVe6WvhaO3fsYwvsYdV7acSrV6rpb7aWAts7P';
	$config['parse_client_key'] = 'VOHsXC6M3rpOHLJ05JToadfS5qCT86UZpfnwyFZ3';
	$config['parse_rest_api_key'] = 'ekZMqg9OUN5fhmmWyipsuOYOiVDsPsO763S8FvNR';
	$config['parse_master_key'] = 'ofHMQfAYyBBuS9u89UoIKulQqxLNQrfs8u1QtmYK';
}
