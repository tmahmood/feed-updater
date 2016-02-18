<?php

include ('libs/dom.php');
include ('app/links.php');

function autoload_classes($class) {
	$app_path = "app/classes/$class.php";
	$lib_path = "libs/$class.php";
	if (file_exists($app_path)) {
		include $app_path;
	} elseif(file_exists($lib_path)) {
		include $lib_path;
	}
}

spl_autoload_register('autoload_classes');


