<?php

$data = array(
	"where"=>array(
		"installationId"=>'023f5557-4e58-47e1-a7df-b8194ee00d61'
		),
	"data"=>array(
		'alert' => 'Curl Testing 6'
		)
	);
$post = json_encode($data);
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://parseapi.back4app.com/push");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

curl_setopt($ch, CURLOPT_POST, 1);

$headers = array();
$headers[] = "X-Parse-Application-Id: 83UvumCLXDMrfNYtxS78YKViHRsd8MZikaHkosAI";
$headers[] = "X-Parse-Master-Key: xhcrYQoTpLWLawP8C5VFgAwyDonwj2U43avkOGyf";
$headers[] = "X-Parse-Rest-Api-Key: i4ZfjpOHWQ6NHiWJqabyFQtRQ36T08Kako1NFYHO";
$headers[] = "Content-Type: application/json";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}else{
			$info = curl_getinfo($ch);
  			echo 'Took ', $info['total_time'], ' seconds to send a request to ', $info['url'], "\n";
		}
curl_close ($ch);
