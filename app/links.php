<?php

function get_unique_article_links($url=null)
{
	if ($url != null) {
		$content = download($url);
		file_put_contents('page.htm', $content);
	}
	$content = file_get_contents('page.htm');
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
	return $outside_links;
}


function get_base_urls($links)
{
	$base_urls = [];
	foreach ($links as $link){
		$base_url = get_base_url($link);
		if (in_array($base_url, array_keys($base_urls))) {
			continue;
		}
		$base_urls[$base_url] = [$base_url, $link];
	}
	return $base_urls;
}


function check_rss_page_exists($links)
{
	$all_headers = [];
	$bad_urls = [];
	foreach ($links as $uinfo){
		$state = check_if_valid_feed($uinfo);
		if ($state === false) {
			$bad_urls[] = $uinfo;
			continue;
		}
		$all_headers[$uinfo[0]] = $state;
	}
	$stage_two_bad_urls = [];
	foreach ($bad_urls as $uinfo){
		$state = check_if_valid_feed($uinfo);
		if ($state === false) {
			$stage_two_bad_urls[] = $uinfo;
			continue;
		}
		$all_headers[$uinfo[0]] = $state;
	}
	return [$all_headers, $stage_two_bad_urls];
}


function check_if_valid_feed($uinfo, $end='/rss')
{
	$base_url = $uinfo[0];
	$url = $base_url . '/' . $end;
	str_replace('//', '/', $url);
	$header = get_site_headers($url);
	$segms = explode(' ', $header[0]);
	if ($segms[1] >= 400) {
		return false;
	}
	$header['feed_url'] = $url;
	$header['source_url'] = $uinfo[1];
	return $header;
}


function check_status_code($sites_headers)
{
	$links_status = [];
	foreach ($sites_headers as $url=>$headers){
		$links_status[$url] = get_status_code($url, $headers);
	}
	return $links_status;
}


function get_status_code($url, $headers)
{
	print_r ($url);
	$segms = explode(' ', $headers[0]);
	if ($segms[1] >= 300) {
		$feed_url = $headers['Location'];
		$source_url = $headers['source_url'];
		$content_type = $headers['Content-Type'][1];
	} else {
		$feed_url = $headers['feed_url'];
		$source_url = $headers['source_url'];
		$content_type = $headers['Content-Type'];
	}
	return [$feed_url, $content_type, $source_url];
}


function save_feeds($fp, $sites_headers, $url)
{
	foreach ($sites_headers as $base_url=>$found){
		save_feed($base_url, $found, $url, $fp);
	}
}


function save_feed($base_url, $found, $url, $fp )
{
	list($feed_url, $content_type, $article) = $found;
	if (strstr($content_type, 'xml') !== false) {
		fputcsv($fp, [$feed_url, $base_url, $url, $article]);
	} else {
		$feeds = search_valid_feed($base_url, $feed_url);
		if (count($feeds) == 0) {
			fputcsv($fp, ['Not found', $base_url, $url, $article]);
		} else {
			foreach ($feeds as $feed){
				fputcsv($fp, [$feed, $base_url, $url, $article]);
			}
		}
	}
}


function search_valid_feed($base_url, $url)
{
	if (is_array($url)) {
		print_r ($url);
	}
	if (trim($url) == '' || trim($url) == '/') {
		return [];
	}
	while(true) {
		$saved_to = download_link($url);
		$content = file_get_contents($saved_to);
		if ($content == '') {
			unlink($saved_to);
			continue;
		}
		break;
	}
	$xpath = get_xpath($content);
	$links = $xpath->query('//a');
	$headers = [];
	$feeds = [];
	$checked = [];
	foreach ($links as $link){
		$url = $link->getAttribute('href');
		if (trim($url) == '' || trim($url) == '/') {
			continue;
		}
		if (in_array($url, $checked)) {
			continue;
		}
		$checked[] = $url;
		if (strpos($url, '/') == 0) {
			$url = $base_url .  $url;
		}
		$header = get_site_headers($url);
		if (!array_key_exists('Content-Type', $header)) {
			continue;
		}
		if(is_array($header['Content-Type'])) {
			$ctype = $header['Content-Type'][1];
		} else {
			$ctype = $header['Content-Type'];
		}
		if (strstr($ctype, 'xml') !== false) {
			$feeds[] = $url;
		}
	}
	return $feeds;
}

