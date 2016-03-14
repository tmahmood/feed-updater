<?php

function get_xpath($content)
{
	try {
		$dom = get_dom($content);
		if ($dom === false) {
			return $false;
		}
		return new DOMXpath($dom);
	} catch (Exception $e) {
		return false;
	}
}

function get_dom($content)
{
	try {
		return @DOMDocument::loadHTML($content);
	} catch (Exception $e) {
		return false;
	}
}

function pl($str)
{
	if (DEBUG == true) {
		printf("%s\n", $str);
	}
}

