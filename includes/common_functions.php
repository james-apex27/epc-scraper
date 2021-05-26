<?php
function get_string_between($string, $start, $end)
{
	$string = " ".$string;
	$ini = strpos($string, $start);
	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string, $end, $ini) - $ini;
	return substr($string, $ini, $len);
}

function replace_new_line($string)
{
	$string = str_replace("\r\n", ', ', $string);
	$string = str_replace("\n", ', ', $string);
	return $string;
}
?>