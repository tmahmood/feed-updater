<?php
include ('bootstrap.php');

chdir(__dir__);

$fp = fopen('sourcelist.csv', 'r');

$runtime = time();
if (!file_exists('csv')) {
	mkdir('csv');
}
$output = "csv/output.csv";
while ($line = fgetcsv($fp)) {
	$app = new App($line[0], $output, true, $runtime);
	$app->run();
}
echo "DONE\n";
