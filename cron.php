<?php
include ('bootstrap.php');

$fp = fopen('sourcelist.csv', 'r');

$output = 'res.csv';
while (true) {
	$line = fgetcsv($fp);
	if ($line == null) {
		break;
	}
	$app = new App($line[0], $output, true);
	$app->run();
}


