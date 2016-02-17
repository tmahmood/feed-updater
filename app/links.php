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
		if (in_array($base_url, $base_urls)) {
			continue;
		}
		$base_urls[] = $base_url;
	}
	return $base_urls;
}

function check_rss_page_exists($links)
{
	$all_headers = [];
	$bad_urls = [];
	foreach ($links as $base_url){
		$url = $base_url . '/rss';
		$header = get_headers($url, 1);
		$segms = explode(' ', $header[0]);
		if ($segms[1] >= 400) {
			$bad_urls[] = $base_url;
			continue;
		}
		$header['feed_url'] = $url;
		$all_headers[$base_url] = $header;
	}
	foreach ($bad_urls as $base_url){
		$url = $base_url . '/feed';
		$header = get_headers($url, 1);
		$header['feed_url'] = $url;
		$all_headers[$base_url] = $header;
	}
	return $all_headers;
}


function check_status_code($sites_headers)
{
	$links_status = [];
	foreach ($sites_headers as $url=>$headers){
		$segms = explode(' ', $headers[0]);
		$cat = 'found';
		if ($segms[1] >= 400) {
			$feed_url = $headers['feed_url'];
			$content_type = null;
			$cat = 400;
		} elseif ($segms[1] >= 300) {
			$feed_url = $headers['Location'];
			$content_type = $headers['Content-Type'];
		} else {
			$feed_url = $headers['feed_url'];
			$content_type = $headers['Content-Type'];
		}
		$links_status[$cat][$url] = [$feed_url, $content_type];
	}
	return $links_status;
}


function download_link($link)
{
	$md5 = md5($link);
	$path = "cache/$md5";
	if (!file_exists($path)) {
		$content = download($link);
		file_put_contents("cache/$md5", $content);
	}
	return $path;
}
