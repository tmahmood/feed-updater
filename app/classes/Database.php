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
		$json = JSON::json_decode_file('.env');
		$this->conn = mysqli_connect($json->db->host, $json->db->user,
										$json->db->pass, $json->db->db);
		if (!$this->conn) {
			die("Failed to connect database");
		}
	}


	function link_already_parsed($url)
	{
		$q = "select * from already_parsed where url='$url'";
		if($result = $this->conn->query($q)) {
			return count($result) >= 0;
		}
		return false;
	}

	function store_parsed_link($url)
	{
		$stmt = $this->conn->prepare("INSERT INTO already_parsed(link) VALUES (?)");
		$stmt->bind_param('s', $url);
		$stmt->execute();
		$stmt->close();
	}
}


