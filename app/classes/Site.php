<?php

/**
 * Class Site
 * @author Tarin Mahmood
 */
class Site
{
	public function __construct($url)
	{
		$this->article_url = $url;
		$this->base_url = Url::get_base_url($url);
		$this->url_info = null;
	}

	function search_valid_feed()
	{
		if($this->check_if_valid_feed('rss')) {
			return true;
		}
		if($this->check_if_valid_feed('feed')) {
			return true;
		}
		$this->feeds = $this->search_links($this->base_url);
		return count($this->feeds) > 0;
	}


	function check_if_valid_feed($end)
	{
		$url = $this->base_url . '/' . trim($end, '/');
		$url_info = new Url($url);
		if ($url_info->fetch_headers() >= 400) {
			return false;
		}
		if($url_info->is_xml_content) {
			$this->url_info = $url_info;
			$this->feeds[] = $url;
			return true;
		}
		$feeds = $this->search_links($url_info->url);
		if (count($feeds) > 0) {
			$this->feeds = $feeds;
			return true;
		}
		return false;
	}

	function write_row($fp, $url)
	{
		$nfeeds = count($this->feeds);
		printf("writing %s link(s)\n", $nfeeds);
		if ($nfeeds == 0) {
			fputcsv($fp, ['', $this->base_url, $url, $this->article_url]);
			return;
		}
		foreach ($this->feeds as $feed){
			fputcsv($fp, [$feed, $this->base_url, $url, $this->article_url]);
		}
	}

	function search_links($url)
	{
		while(true) {
			$saved_to = Url::download_link($url);
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
			if($this->should_skip($url, $checked)) {
				continue;
			}
			$checked[] = $url;
			$url = Url::fix($url, $this->base_url);
			$url_info = new Url($url);
			if($url_info->fetch_headers() >= 400) {
				continue;
			}
			if ($url_info->is_xml_content) {
				$feeds[] = $url;
			}
			if (count($checked) % 30 == 0) {
				printf("#", count($checked));
			}
		}
		print("\n");
		return $feeds;
	}


	function should_skip($url, $checked)
	{
		if (strstr($url, 'javascript:') !== false) {
			return true;
		}
		if (strstr($url, 'mailto:') !== false) {
			return true;
		}
		if (trim($url) == '' || trim($url) == '/') {
			return true;
		}
		if (in_array($url, $checked)) {
			return true;
		}
		return false;
	}
}

