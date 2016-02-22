<?php

/**
 * Class Database
 * @author Tarin Mahmood
 */
class Database
{
	public function __construct()
	{
		// do database connection here
		$this->db = 'cache/url_parsed.json';
	}


	function check_db()
	{
		if (isset($this->already_parsed)) {
			return;
		}
		if (file_exists($this->db)) {
			return $this->already_parsed = JSON::json_decode_file($this->db);
		}
		JSON::json_encode_to_file($this->db, []);
		return $this->already_parsed = [];
	}


	function link_already_parsed($url)
	{
		$this->check_db();
		return in_array($url, $this->already_parsed);
	}

	function store_parsed_link($url)
	{
		$this->check_db();
		if (in_array($url, $this->already_parsed)) {
			return;
		}
		$this->already_parsed[] = $url;
		JSON::json_encode_to_file($this->db, $this->already_parsed);
	}
}


