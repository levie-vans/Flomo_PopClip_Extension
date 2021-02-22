<?php

function force_string($str) {
    return is_string($str)?$str:'';
}

$api=trim(force_string(getenv('POPCLIP_OPTION_API')));
$content=trim(force_string(getenv('POPCLIP_TEXT')));
$tag = getenv('POPCLIP_OPTION_TAG');
$browser_title = getenv("POPCLIP_BROWSER_TITLE");
$browser_url = getenv("POPCLIP_BROWSER_URL");


$ch = curl_init($api);
curl_setopt($ch, CURLOPT_URL, $api);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json; charset=utf-8',
]);

if ($browser_title != '' && $browser_url != '') {
	$content .= "\n\n网页标题：{$browser_title}";
	$content .= "\nURL：{$browser_url}";
  }
  
  if ($tag != '') {
	$content .= "\n\n#{$tag}";
  }  

$json_array = [
  'content' => $content
]; 
$body = json_encode($json_array);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($code===200) {
	$data=json_decode($response, TRUE);
	$result=trim(force_string($data['message']));
	if(strlen($result) > 0) {
		echo($result);
		exit(0);
	}
}else if ($code==403) {
	echo("Auth Error");
	exit(2); // bad auth
}

# bad response
echo("Send failed");
exit(1);