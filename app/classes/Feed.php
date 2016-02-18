<?php

/**
 * Class Feed
 * @author Tarin Mahmood
 */
class Feed
{
	public static function save_feed($site)
	{
		if (strstr($content_type, 'xml') !== false) {
			fputcsv($fp, [$feed_url, $base_url, $url, $article]);
		} else {
			$feeds = search_valid_feed($base_url, $feed_url);
			if (count($feeds) == 0) {
				fputcsv($fp, ['Not found', $base_url, $url, $article]);
			} else {
				foreach ($feeds as $feed){
					fputcsv($fp, [$feed, $base_url, $url, $article]);
				}
			}
		}
	}

}


