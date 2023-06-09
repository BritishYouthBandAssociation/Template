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
	$msg = "An error occurred whilst generating the requested template.";

	if(defined("DEBUG") && DEBUG){
		$msg .= "<br />" . $e;
	}

	die($msg);
}

$template->output($config);

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