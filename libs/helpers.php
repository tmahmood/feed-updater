<?php

function get_xpath($content)
{
	try {
		$dom = get_dom($content);
		return new DOMXpath($dom);
	} catch (Exception $e) {
		return false;
	}
}

function get_dom($content)
{
	return @DOMDocument::loadHTML($content);
}

function pl($str)
{
	if (DEBUG == true) {
		printf("%s\n", $str);
	}
}

