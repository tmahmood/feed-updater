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

