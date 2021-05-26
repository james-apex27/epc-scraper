<?php
function curl_request($url)
{
	$curl = curl_init();
	$ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_VERBOSE, FALSE);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, FALSE);
	curl_setopt($curl, CURLOPT_URL, $url);
	#curl_setopt($curl, CURLOPT_REFERER, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,10);


	/*curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); 
	curl_setopt($curl, CURLOPT_TIMEOUT, 5); //timeout in seconds*/
	/*curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
	curl_setopt($curl, CURLOPT_TIMEOUT, 3);*/
	$str = curl_exec($curl);
	curl_close($curl);
	return $str;
}
?>