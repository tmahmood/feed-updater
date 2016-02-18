<?php

include ('bootstrap.php');

if (count($argv) > 2) {
	$url = $argv[1];
	$filename = $argv[2];
} else {
	$url = 'https://flipboard.com/topic/personalfinance';
	$filename = 'feedsources.csv';
}

$urls = Url::get_unique_article_links($url);

$fp = fopen($filename, 'w');
fputcsv($fp, ['feed url', 'website url', 'source', 'article']);

foreach ($urls as $url){
	printf("checking: %s\n", $url);
	$site = new Site($url);
	if($site->search_valid_feed()) {
		$site->write_row($fp, 'test');
	}
}
fclose($fp);
