<?php

/**
 * Class SiteTest
 * @author Tarin Mahmood
 */
class UrlTest extends PHPUnit_Framework_TestCase
{
	public function test200()
	{
		$test_url = 'http://www.telegraph.co.uk/rss';
		$uinfo = new Url($test_url);
		$this->assertEquals($uinfo->fetch_headers(), 200);
		$this->assertFalse($uinfo->redirected);
		$this->assertTrue($uinfo->is_xml_content);
	}

	public function test301()
	{
		$test_url = 'http://www.telegraph.co.uk/feed';
		$uinfo = new Url($test_url);
		$this->assertEquals($uinfo->fetch_headers(), 301);
		$this->assertTrue($uinfo->redirected);
		$this->assertFalse($uinfo->is_xml_content);
	}

	public function testUrlFixer()
	{
		$base_url = 'http://www.businessinsider.com';
		$q1 = 'database.html';
		$q2 = '/data/book/23';
		$q3 = 'http://telegraph.co.uk';
		$res = Url::fix($q1, $base_url);
		$this->assertEquals('http://www.businessinsider.com/database.html', $res);
		$res = Url::fix($q2, $base_url);
		$this->assertEquals('http://www.businessinsider.com/data/book/23', $res);
		$res = Url::fix($q3, $base_url);
		$this->assertEquals($q3, $res);

	}


}
