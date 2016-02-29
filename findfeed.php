<?php
include ('bootstrap.php');
if (count($_POST) > 2) {
	$url = $_POST['url'];
	$filename = $_POST['filename'];
} elseif (count($argv) > 2) {
	$url = $argv[1];
	$filename = $argv[2];
} else {
	die("You need to provide URL and Output file name, Please read the readme.md\n");
}
$db = new Database();
$urls = Url::get_unique_article_links($url);
$fp = fopen($filename, 'w');
fputcsv($fp, ['source', 'article','website url', 'feed url' ]);
foreach ($urls as $article){
	$site = new Site($article);
	if ($db->link_already_parsed($site->base_url)) {
		continue;
	}
	pl("deep search $site->base_url");
	if($site->search_valid_feed()) {
		$site->write_row($fp, $url);
	}
	$db->store_parsed_link($site->base_url);
}
fclose($fp);
