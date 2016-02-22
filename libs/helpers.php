<?php

function get_xpath($content)
{
	$dom = get_dom($content);
	return new DOMXpath($dom);
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

