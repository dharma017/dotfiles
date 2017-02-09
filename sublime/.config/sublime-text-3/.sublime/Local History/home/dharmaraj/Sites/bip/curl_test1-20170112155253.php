<?php

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://parseapi.back4app.com/classes/_Installation");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

// curl_setopt($ch, CURLOPT_POST, 1);

$headers = array();
$headers[] = "X-Parse-Application-Id: 83UvumCLXDMrfNYtxS78YKViHRsd8MZikaHkosAI";
$headers[] = "X-Parse-Master-Key: xhcrYQoTpLWLawP8C5VFgAwyDonwj2U43avkOGyf";
$headers[] = "X-Parse-Rest-Api-Key: i4ZfjpOHWQ6NHiWJqabyFQtRQ36T08Kako1NFYHO";
// $headers[] = "Content-Type: application/json";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}else{
			$info = curl_getinfo($ch);
  			// echo 'Took ', $info['total_time'], ' seconds to send a request to ', $info['url'], "\n";
		}
curl_close ($ch);
$result_array = json_decode($result,true);

$installations = array();
foreach ($result_array as $key => $row_array) {
	foreach ($row_array as $key => $row) {
		$installations[] = $row['installationId'];
	}
}
echo "<pre>";print_r($installations);exit;
