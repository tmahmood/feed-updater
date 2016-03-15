<?php

/**
 * Class App
 * @author Tarin Mahmood
 */
class App
{
	public function __construct($url, $filename, $append=false)
	{
		$this->db = new CSVDatabase();
		$this->filename = $filename;
		$this->url = $url;
		if ($append) {
			$this->fp = fopen($this->filename, 'a+');
		} else {
			$this->fp = fopen($this->filename, 'w');
			$this->write_headers();
		}
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
			if ($this->db->link_already_parsed($site->base_url)) {
				continue;
			}
			pl("deep search $site->base_url");
			if($site->search_valid_feed()) {
				$site->write_row($this->fp, $this->url);
			}
			$this->db->store_parsed_link($site->base_url);
		}
		fclose($this->fp);
	}

}


