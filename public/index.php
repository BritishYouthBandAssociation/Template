<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../lib/Utils.php");

if(!isset($_GET['image'])){
    die("Missing image!");
}

if(!isset($_GET['text'])){
    die("Missing text!");
}

$wrapLength = isset($_GET['wrap']) ? $_GET['wrap'] : 16;

define("TARGET_SIZE", 1000);

$image = loadRemoteImage($_GET['image']);


$text = wordwrap(strtoupper($_GET['text']), $wrapLength, "\n");



if($image == null){
    die("Unsupported image type");
}

$canvas = imagecreatetruecolor(TARGET_SIZE, TARGET_SIZE);
fitImageToCanvas($image, $canvas);
alphaGradient($canvas, 0, TARGET_SIZE * 0.2, TARGET_SIZE, TARGET_SIZE, 127, 10, 0x282360);
writeCenteredTtfText($canvas, "../asket.ttf", $text, imagecolorallocate($image,255,255,255), TARGET_SIZE / 6, TARGET_SIZE * 0.75, TARGET_SIZE * 0.666, TARGET_SIZE * 0.1);

header("Content-Type: image/png");
imagepng($canvas);