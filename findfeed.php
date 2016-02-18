<?php

include ('bootstrap.php');

$urls = [
			"http://www.telegraph.co.uk/finance/personalfinance/investing/shares/12160028/Sell-off-has-barely-begun-says-1.2bn-investor.html"
];

foreach ($urls as $url){
	$site = new Site($url);
	if($site->check_if_valid_feed('rss')) {
		if ($site->is_xml_content) {
			fputcsv($fp, $site->get_row());
		} else {
			$feeds = $site->search_valid_feed();
			if (count($feeds) == 0) {
				fputcsv($fp, ['Not found', $base_url, $url, $article]);
			} else {
				foreach ($feeds as $feed){
					fputcsv($fp, [$feed, $base_url, $url, $article]);
				}
			}
		}
	}
	if($site->check_if_valid_feed('feed')) {
		print_r ($site->header);
	}
	var_dump($site);
}




