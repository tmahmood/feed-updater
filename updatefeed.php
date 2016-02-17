<?php

include ('libs/download.php');
include ('libs/dom.php');
include ('libs/helpers.php');
include ('app/links.php');


//$url = 'https://flipboard.com/topic/3dmodeling';
//$outside_links = get_unique_article_links();
//$base_urls = get_base_urls($outside_links);
//$all_headers = check_rss_page_exists($base_urls);
//json_encode_to_file('headers', $all_headers);
$sites_headers = check_status_code(json_decode_file('headers'));
print_r($sites_headers);
foreach ($sites_headers['found'] as $found){
	$saved_to = download_link($found[0]);
}
