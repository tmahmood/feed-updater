<?php

/**
 * Class SiteTest
 * @author Tarin Mahmood
 */
class SiteTest extends PHPUnit_Framework_TestCase
{
	public function testBaseUrl()
	{
		$test_url = 'http://www.telegraph.co.uk/finance/personalfinance/investing/shares/12160028/Sell-off-has-barely-begun-says-1.2bn-investor.html';
		$site = new Site($test_url);
		$this->assertEquals('http://www.telegraph.co.uk', $site->base_url);
	}

	public function testGoodRSS()
	{
		$test_url = 'http://www.telegraph.co.uk/finance/personalfinance/investing/shares/12160028/Sell-off-has-barely-begun-says-1.2bn-investor.html';
		$site = new Site($test_url);
		$state = $site->check_if_valid_feed('rss');
		$this->assertTrue($state);
	}

	public function testBadRSS()
	{
		$test_url = 'http://www.cgflores.com/p/obsidian-h.html';
		$site = new Site($test_url);
		$state = $site->check_if_valid_feed('rss');
		$this->assertFalse($state);
	}

	public function testDownload()
	{
		$url = 'https://www.washingtonpost.com/rss';
		$content = Url::download_link($url);
		$xpath = get_xpath($content);
		$links = $xpath->query('//a');
		$this->assertEquals(290, $links->length);
		$site = new Site($url);
		$feeds = $site->check_all_links_in_page($content);
	}

	public function testGetFeeds()
	{
		$url = 'https://www.washingtonpost.com/rss';
		$site = new Site($url);
		$found = $site->search_links($url);
		$this->assertEquals(67, count($found));
	}

	public function testStoreLink()
	{
		$db = new Database();
		$db->clear_links_table();
		$db->store_parsed_link('test_link', json_encode(['link1', 'link2']));
		$exists = $db->link_already_parsed('test_link');
		$this->assertTrue($exists);
		$exists = $db->link_already_parsed('test_link_2');
		$this->assertFalse($exists);
	}

}
