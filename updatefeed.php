<?php

include ('libs/download.php');
include ('libs/dom.php');
include ('libs/helpers.php');

//$url = 'https://flipboard.com/topic/3dmodeling';
//$content = download($url);
$content = file_get_contents('3dmodeling.htm');
$xpath = get_xpath($content);
$links = $xpath->query('//a');
$outside_links = [];
foreach ($links as $link){
	$target_url = $link->getAttribute('href');
	if (strpos($target_url, '/') == 0) {
		continue;
	}
	$outside_links[] = $target_url;
}
var_export($outside_links);
$base_urls = [];
foreach ($outside_links as $link){
	$base_url = get_base_url($link);
	if (in_array($base_url, $base_urls)) {
		continue;
	}
	$base_urls[] = $base_url;
}
var_export($base_urls);
$all_headers = [];

foreach ($base_urls as $base_url){
	// step 1
	$url = $base_url . '/rss';
	$all_headers[$base_url] = get_headers($url, 1);

}
var_export($all_headers);

