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
		$json = JSON::json_decode_file('.env', true);
		$this->conn = mysqli_connect($json->db->host, $json->db->user,
										$json->db->pass, $json->db->db);
		if (!$this->conn) {
			die("Failed to connect database");
		}
	}

	function link_already_parsed($url)
	{
		$query = "select * from parsed_links where link='$url'";
		return $this->count_query($query) > 0;
	}

	function store_parsed_link($url, $feeds)
	{
		$q = "INSERT INTO parsed_links(link, feeds) VALUES (?, ?)";
		$stmt = $this->conn->prepare($q);
		$stmt->bind_param('ss', $url, $feeds);
		$stmt->execute();
		$stmt->close();
	}

	function clear_links_table()
	{
		$this->conn->query('truncate parsed_links');
	}

	function get_feeds($link)
	{
		$query = "select feeds from parsed_links where link='$link'";
		if ($result = $this->conn->query($query)) {
			$d = $result->fetch_row();
			return json_decode($d[0]);
		}
		return false;
	}

	public function link_already_exported($link, $runtime)
	{
		$query = "select * from exported_links where link='$link' and runtime=$runtime";
		return $this->count_query($query) > 0;
	}

	public function link_is_exported($link, $runtime)
	{
		$q = "INSERT INTO exported_links(link, runtime) VALUES (?, ?)";
		$stmt = $this->conn->prepare($q);
		$stmt->bind_param('ss', $link, $runtime);
		$stmt->execute();
		$stmt->close();
	}

	function get_runtime_links_count($runtime)
	{
		$query = "select link from exported_links where runtime='$runtime'";
		return $this->count_query($query);
	}

	function count_query($query)
	{
		if ($stmt = $this->conn->prepare($query)) {
			$stmt->execute();
			$stmt->store_result();
			return $stmt->num_rows;
		}
		return false;

	}
}

