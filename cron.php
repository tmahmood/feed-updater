<?php
include ('bootstrap.php');

$fp = fopen('sources.csv', 'r');

$runtime = time();
$output = "res_$runtime.csv";
while (true) {
	$line = fgetcsv($fp);
	if ($line == null) {
		break;
	}
	$app = new App($line[0], $output, true, $runtime);
	$app->run();
}
