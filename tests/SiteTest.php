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


	function testGetFeeds()
	{
		$base_url = 'https://www.washingtonpost.com/';
		$site = new Site($base_url);
		$cnt = $site->search_valid_feed();
		$this->assertEquals(67, $cnt);
	}
}
