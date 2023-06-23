<?php

@include("../env.php");
define("TEMPLATE_PATH", __DIR__ . "/../lib/Template/");

require_once("../lib/Utils.php");
require_once("../lib/Config.php");

$config = Config::load();
$template = resolveTemplate($config);

try{
	$template->parseParams();
	$template->render();
} catch (Exception $e){
	die("An error occurred whilst generating the requested template.");
}

output($template, $config);

function resolveTemplate($config){
	if(!isset($_GET['template'])){
		die("Missing template");
	}

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

function output($template, $config){
	$type = $config->outputType;
	header("Content-Type: image/$type");
	header("Content-Disposition: attachment; filename=\"$template->fileName.$type\"");

	switch($type){
		case "gif":
			return imagegif($template->canvas);
		case "jpeg":
			return imagejpeg($template->canvas);
		case "bmp":
			return imagebmp($template->canvas);
		case "webp":
			return imagewebp($template->canvas);
		default:
			return imagepng($template->canvas);
	}
}