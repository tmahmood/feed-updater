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

$app = new App($url, $filename);
$app->run();
