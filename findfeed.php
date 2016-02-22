<?php
include ('bootstrap.php');
if (count($argv) > 2) {
	$url = $argv[1];
	$filename = $argv[2];
} else {
	$url = 'https://flipboard.com/topic/personalfinance';
	$filename = 'feedsources.csv';
}
$db = new Database();
$urls = Url::get_unique_article_links($url);
$fp = fopen($filename, 'w');
fputcsv($fp, ['source', 'article','website url', 'feed url' ]);
foreach ($urls as $article){
	$site = new Site($article);
	if ($db->link_already_parsed($site->base_url)) {
		continue;
	}
	print("@");
	if($site->search_valid_feed()) {
		$site->write_row($fp, $url);
	}
	$db->store_parsed_link($site->base_url);
}
fclose($fp);
