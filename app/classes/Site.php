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
		$this->found = null;
		$this->content_type = null;
		$this->feed_url = null;
		$this->is_xml_content = null;
		$this->redirected = null;
		$this->header = null;
	}

	function check_if_valid_feed($end)
	{
		$url = $this->base_url . '/' . $end;
		str_replace('//', '/', $url);
		$header = Url::get_site_headers($url);
		$segms = explode(' ', $header[0]);
		if ($segms[1] >= 400) {
			return false;
		}
		$this->found = true;
		$this->header = $header;
		if ($segms[1] >= 300) {
			$this->feed_url = $header['Location'];
			$this->redirected = true;
			$this->content_type = $this->header['Content-Type'][1];
			$this->is_xml_content = strstr($this->content_type, 'xml') !== false;
		} else {
			$this->feed_url = $url;
			$this->content_type = $this->header['Content-Type'];
			$this->is_xml_content = strstr($this->content_type, 'xml') !== false;
		}
		return true;
	}

	function get_row()
	{
		return [ $this->feed_url, $this->base_url, $null, $this->article_url ];
	}

	function search_valid_feed()
	{
		while(true) {
			$saved_to = download_link($this->feed_url);
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
		$this->feed_url = $feeds;
		return count($feeds);
	}
}
