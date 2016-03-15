<?php

/**
 * Class App
 * @author Tarin Mahmood
 */
class App
{
	public function __construct($url, $filename, $append=false, $runtime=null)
	{
		$this->db = new Database();
		$this->filename = $filename;
		$this->url = $url;
		if ($append) {
			$this->fp = fopen($this->filename, 'a+');
		} else {
			$this->fp = fopen($this->filename, 'w');
			$this->write_headers();
		}
		$this->runtime = $runtime;
	}

	function write_headers()
	{
		fputcsv($this->fp, ['source', 'article','website url', 'feed url' ]);
	}

	function run()
	{
		$urls = Url::get_unique_article_links($this->url);
		foreach ($urls as $article){
			$site = new Site($article);
			if($this->check_runtime($site)) {
				continue;
			}
			if($this->site_already_parsed($site)) {
				continue;
			}
			pl("deep search $site->base_url");
			if($site->search_valid_feed()) {
				$site->write_row($this->fp, $this->url);
			}
			$this->db->store_parsed_link($site->base_url, json_encode($site->feeds));
		}
		fclose($this->fp);
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
