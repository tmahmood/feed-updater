<?php

const USER_AGENT = '"Mozilla/5.0 (X11; Linux x86_64; rv:36.0) Gecko/20100101 Firefox/36.0"';
const COOKIE_PATH = 'cookie.jar';


/**
 * Class Url
 * @author Tarin Mahmood
 */
class Url
{
	function __construct($url)
	{
		$this->url = $url;
	}

	function fetch_headers()
	{
		$headers = Url::get_site_headers($this->url);
		$this->headers = $headers;
		$segms = explode(' ', $headers[0]);
		if (count($segms) < 2) {
			return 404;
		}
		$this->status_code = $segms[1];
		if ($this->status_code >= 400) {
			return $this->status_code;
		}
		if (!array_key_exists('Content-Type', $headers) ||
				!array_key_exists('content-type', $headers)) {
			if ($this->status_code == 204) {
				return $this->status_code;
			}
		}
		$ctype = array_key_exists('Content-Type', $headers) ? 'Content-Type' : 'content-type';
		if ($this->status_code < 300) {
			$this->redirected = false;
			$this->content_type = $headers[$ctype];
		} else {
			$this->redirected = true;
			if (array_key_exists('Location', $headers)) {
				$this->location = $headers['Location'];
			} else {
				$this->location = $headers['location'];
			}
			$this->content_type = $headers[$ctype][1];
		}
		$this->is_xml_content = strstr($this->content_type, 'xml') !== false;
		return $this->status_code;
	}


	// {{{ static functions

	public static function get_unique_article_links($url)
	{
		$content = Url::download($url);
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


	public static function download($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIE_PATH);
		curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIE_PATH);
		$response = curl_exec($ch);
		curl_close($ch);
		return str_replace(array('<br>', '<br/>', '<br />'), "\n", $response);
	}

	public static function download_link($link, $tries=0)
	{
		$md5 = md5($link);
		$path = "cache/$md5";
		if (DEBUG == true) {
			if ($tries > 0) {
				unlink($path);
			}
			if (file_exists($path)) {
				return file_get_contents($path);;
			}
		}
		$content = Url::download($link);
		if (DEBUG == true) {
			file_put_contents($path, $content);
		}
		return $content;
	}

	public static function get_site_headers($url)
	{
		if (DEBUG == true) {
			$path = 'cache/headers/' . md5($url);
			if (file_exists($path)) {
				$header = JSON::json_decode_file($path);
				if ($header != false) {
					return $header;
				}
			}
		}
		$header = @get_headers($url, 1);
		if (DEBUG == true) {
			JSON::json_encode_to_file($path, $header);
		}
		return $header;
	}

	public static function get_base_url($url)
	{
		$urldetails = parse_url($url);
		return $urldetails['scheme'] . '://' . $urldetails['host'];
	}

	public static function fix($url, $base_url)
	{
		$uobj = parse_url($url);
		if ($uobj == false) {
			pl("FAILED: $url", $url);
			return $url;
		}
		if (array_key_exists('scheme', $uobj)) {
			return $url;
		}
		return $base_url . '/' . trim($url, '/');
	}
	// }}}
}


