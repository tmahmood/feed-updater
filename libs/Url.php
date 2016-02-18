<?php

const USER_AGENT = '"Mozilla/5.0 (X11; Linux x86_64; rv:36.0) Gecko/20100101 Firefox/36.0"';
const COOKIE_PATH = 'cookie.jar';


/**
 * Class Url
 * @author Tarin Mahmood
 */
class Url
{
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

	public static function download_link($link)
	{
		$md5 = md5($link);
		$path = "cache/$md5";
		if (!file_exists($path)) {
			$content = Url::download($link);
			file_put_contents("cache/$md5", $content);
		}
		return $path;
	}

	public static function get_site_headers($url)
	{
		$path = 'cache/headers/' . md5($url);
		if (file_exists($path)) {
			$header = JSON::json_decode_file($path);
		} else {
			$header = get_headers($url, 1);
			JSON::json_encode_to_file($path, $header);
		}
		return $header;
	}

	public static function get_base_url($url)
	{
		$urldetails = parse_url($url);
		return $urldetails['scheme'] . '://' . $urldetails['host'];
	}

}


