<?php

function println($text)
{
	print("$text\n");
}

function get_base_url($url)
{
	$urldetails = parse_url($url);
	return $urldetails['scheme'] . '://' . $urldetails['host'];
}

function json_decode_file($file_name)
{
	$json = file_get_contents($file_name);
	return json_decode($json, 1);
}

function json_encode_to_file($file_name, $content)
{
	file_put_contents($file_name, json_encode($content));
}

