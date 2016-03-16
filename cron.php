<?php
include ('bootstrap.php');

$fp = fopen('sources.csv', 'r');

$runtime = time();
$output = "res_$runtime.csv";
while ($line = fgetcsv($fp)) {
	$app = new App($line[0], $output, true, $runtime);
	$app->run();
}
