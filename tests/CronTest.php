<?php

/**
 * Class CronTest
 * @author Tarin Mahmood
 */
class CronTest extends PHPUnit_Framework_TestCase
{
	public function testListOfUrls()
	{
		$outfile = 'out.csv';
		if(file_exists($outfile)) {
			unlink('out.csv');
		}
		$lists = [
					['https://www.washingtonpost.com'],
					['http://cnet.com/']
				];

		$runtime = time();
		foreach ($lists as $list){
			$app = new App($list, $outfile, true, $runtime);
			$app->run();
		}
		$db = new Database();
		$cnt = $db->get_runtime_links_count($runtime);
		$this->assertEquals(2, $cnt);
		$lines = explode("\n", file_get_contents($outfile));
		$this->assertEquals(71, count($lines));
	}
}
