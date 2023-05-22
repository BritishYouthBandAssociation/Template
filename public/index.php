<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("TEMPLATE_PATH", __DIR__ . "/../lib/Template/");

require_once("../lib/Utils.php");
require_once("../lib/Config.php");

$config = Config::load();

$template = resolveTemplate($config);
$template->parseParams();

$image = $template->render();

header("Content-Type: image/png");
imagepng($image);

function resolveTemplate($config){
	$rawTemplate = $_GET['template'] . "Template.php";
	$fileName = null;
	$files = scandir(TEMPLATE_PATH);

	foreach($files as $file){
		if(strcasecmp($rawTemplate, $file) == 0){
			$fileName = $file;
		}
	}

	if($fileName == null){
		die("Missing template");
	}

	$className = pathinfo($fileName, PATHINFO_FILENAME);

	require_once(TEMPLATE_PATH . $fileName);
	return new $className($config);
}