<?php

include ('libs/download.php');
include ('libs/dom.php');
include ('libs/helpers.php');
include ('app/links.php');


$url = 'https://flipboard.com/topic/3dmodeling';
$outside_links = get_unique_article_links();
$base_urls = get_base_urls($outside_links);
list($good_headers, $bad_urls) = check_rss_page_exists($base_urls);
$sites_headers = check_status_code($good_headers);
$fp = fopen('feedsources.csv', 'w');
fputcsv($fp, ['feed url', 'website url', 'source', 'article']);
save_feeds($fp, $sites_headers, $url);
foreach ($bad_urls as $found){
	list($base_url, $article) = $found;
	printf("checking: %s\n", $base_url);
	$feeds = search_valid_feed($base_url, $base_url);
	if (count($feeds) > 0) {
		foreach ($feeds as $feed){
			save_feed($base_url, [$feed, 'xml', $article], $url, $fp);
		}
	}
}
fclose($fp);
