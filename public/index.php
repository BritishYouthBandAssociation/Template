<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../lib/Utils.php");
require_once("../lib/Template/InstagramNewsTemplate.php");

if(!isset($_GET['image'])){
    die("Missing image!");
}

if(!isset($_GET['text'])){
    die("Missing text!");
}

$wrapLength = isset($_GET['wrap']) ? $_GET['wrap'] : 16;
$text = wordwrap(strtoupper($_GET['text']), $wrapLength, "\n");

$image = loadRemoteImage($_GET['image']);

if($image == null){
	die("Unsupported image type");
}

$template = new InstagramNewsTemplate();
$canvas = $template->render($image, $text);

header("Content-Type: image/png");
imagepng($canvas);