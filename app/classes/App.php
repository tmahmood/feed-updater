<?php

/**
 * Class App
 * @author Tarin Mahmood
 */
class App
{
	public function __construct($source, $filename, $append=false, $runtime=null)
	{
		$this->db = new Database();
		$this->filename = $filename;
		$this->source = $source;
		$this->appending = $append;
		$this->runtime = $runtime;
	}

	function run()
	{
		$this->open_file();
		if (!$this->appending) {
			App::write_headers($this->fp);
		}
		$urls = $this->get_source();
		foreach ($urls as $article){
			$this->parse_article($article);
		}
		fclose($this->fp);
	}

	function open_file()
	{
		if ($this->appending) {
			$this->fp = fopen($this->filename, 'a+');
		} else {
			$this->fp = fopen($this->filename, 'w');
		}
	}

	public static function write_headers($fp)
	{
		fputcsv($fp, ['source','article','site_title','image','website url','feed url']);
	}

	function get_source()
	{
		if (is_array($this->source)) {
			$this->url = 'LIST';
			return $this->source;
		} else {
			$this->url = $this->source;
			return Url::get_unique_article_links($this->source);
		}
	}

	function parse_article($article)
	{
		$site = new Site($article);
		if($this->check_runtime($site)) {
			return;
		}
		if($this->site_already_parsed($site)) {
			return;
		}
		if($site->search_valid_feed()) {
			$site->write_row($this->fp, $this->url);
		}
		$this->db->store_parsed_link($site->base_url, json_encode($site->feeds));
	}


	function site_already_parsed($site)
	{
		if (!$this->db->link_already_parsed($site->base_url)) {
			return false;
		}
		$site->feeds = $this->db->get_feeds($site->base_url);
		$site->write_row($this->fp, $this->url);
		return true;
	}


	function check_runtime($site)
	{
		if($this->runtime != null) {
			if ($this->db->link_already_exported($site->base_url, $this->runtime)) {
				return true;
			}
			$this->db->link_is_exported($site->base_url, $this->runtime);
		}
		return false;
	}
}
