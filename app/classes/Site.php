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
		$this->site_title = '';
		$this->site_image = '';
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
		$this->find_info();
		$nfeeds = count($this->feeds);
		pl("saving: {$this->base_url}");
		if ($nfeeds == 0) {
			$this->write($fp, $url);
			return;
		}
		foreach ($this->feeds as $feed){
			$this->write($fp, $url, $feed);
		}
	}

	private function write($fp, $url, $feed='N/A')
	{
		fputcsv($fp, [$url, $this->base_url, $this->site_title,
							$this->site_image, $this->article_url,
							$feed]);
	}

	function search_links($url)
	{
		$tries = 0;
		while(true) {
			pl("searching $url ($tries) ...");
			$content = Url::download_link($url, $tries);
			if ($content != '') {
				break;
			}
			$tries++;
			if ($tries >= 3) {
				return [];
			}
		}
		return $this->check_all_links_in_page($content);
	}


	function check_all_links_in_page($content)
	{
		$xpath = get_xpath($content);
		if ($xpath === false) {
			return [];
		}
		$links = $xpath->query('//a');
		$headers = [];
		$feeds = [];
		$checked = [];
		foreach ($links as $link){
			$this->validate_link($link, $feeds, $checked);
		}
		if (count($checked) > 30) {
			print("\n");
		}
		return $feeds;
	}

	function validate_link($link, &$feeds, &$checked)
	{
		$url = $link->getAttribute('href');
		if($this->should_skip($url, $checked)) {
			return;
		}
		$checked[] = $url;
		$url = Url::fix($url, $this->base_url);
		$url_info = new Url($url);
		if($url_info->fetch_headers() >= 400) {
			return;
		}
		if ($url_info->is_xml_content) {
			$feeds[] = $url;
		}
		if (count($checked) % 30 == 0) {
			print("#");
		}
	}

	function should_skip($url, $checked)
	{
		if (strstr($url, 'javascript:') !== false) {
			return true;
		}
		if (strstr($url, 'JavaScript:') !== false) {
			return true;
		}
		if (strstr($url, 'mailto:') !== false) {
			return true;
		}
		if (trim($url) == '' || trim($url) == '/' || trim($url) == '#') {
			return true;
		}
		if (in_array($url, $checked)) {
			return true;
		}
		if (strpos($url, '#') === 0) {
			return true;
		}
		return false;
	}

	function find_info()
	{
		$content = Url::download_link($this->base_url);
		$xpath = get_xpath($content);
		if ($xpath === false) {
			return;
		}
		$res = $xpath->query('//meta[@property="og:site_name"]');
		if ($res->length > 0) {
			$this->site_title = $res->item(0)->getAttribute('content');
		} else {
			$res = $xpath->query('//title');
			if ($res->length > 0) {
				$this->site_title = $res->item(0)->textContent;
			}
		}
		$res = $xpath->query('//meta[@property="og:image"]');
		if ($res->length > 0) {
			$this->site_image = $res->item(0)->getAttribute('content');
			return;
		}
		$res = $xpath->query('id("logo")//img');
		if ($res->length >0) {
			$this->site_image = $res->item(0)->getAttribute('src');
			return;
		}
		$res = $xpath->query('//*[@class="logo"]/img');
		if ($res->length >0) {
			$this->site_image = $res->item(0)->getAttribute('src');
			return;
		}
		$res = $xpath->query("//img[contains(@src, 'logo')]");
		if ($res->length >0) {
			$this->site_image = $res->item(0)->getAttribute('src');
			return;
		}
	}
}

