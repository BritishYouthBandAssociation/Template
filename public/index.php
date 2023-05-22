<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_GET['image'])){
    die("Missing image!");
}

if(!isset($_GET['text'])){
    die("Missing text!");
}

$wrapLength = isset($_GET['wrap']) ? $_GET['wrap'] : 16;

define("TARGET_SIZE", 1000);

$image = loadRemoteImage($_GET['image']);
$w = imagesx($image);
$h = imagesy($image);

$text = wordwrap(strtoupper($_GET['text']), $wrapLength, "\n");

if($w >= $h){
    $multiplier = $h / TARGET_SIZE;
    $srcX = $w / 4;
    $srcY = 0;
} else {
    $multiplier = $w / TARGET_SIZE;
    $srcX = 0;
    $srcY = $h / 4;
}

$targetW = $w / $multiplier;
$targetH = $h / $multiplier;

if($image == null){
    die("Unsupported image type");
}

$canvas = imagecreatetruecolor(TARGET_SIZE, TARGET_SIZE);
imagecopyresized($canvas, $image, 0, 0, round($srcX), round($srcY), round($targetW), round($targetH), $w, $h);
alphaGradient($canvas, 0, TARGET_SIZE * 0.2, TARGET_SIZE, TARGET_SIZE, 127, 10, 0x282360);

writeCenteredTtfText($canvas, "../asket.ttf", $text, imagecolorallocate($image,255,255,255), TARGET_SIZE / 6, TARGET_SIZE * 0.75, TARGET_SIZE * 0.666, TARGET_SIZE * 0.1);

header("Content-Type: image/png");
imagepng($canvas);

function loadRemoteImage($url){
    $filePath = tempnam(sys_get_temp_dir(), "IMG_");
    $data = file_get_contents($url);
    file_put_contents($filePath, $data);

    $type = exif_imagetype($filePath);
    switch ($type){
        case 1:
            return imageCreateFromGif($filePath);
        case 2:
            return imageCreateFromJpeg($filePath);
        case 3:
            return imageCreateFromPng($filePath);
        case 6:
            return imageCreateFromBmp($filePath);
        default:
            return null;
    }
}

function alphaGradient($image, $left, $top, $right, $bottom, $startAlpha, $endAlpha, $colour = 0){
    $r = ($colour & 0xFF0000) >> 16;
    $g = ($colour & 0x00FF00) >> 8;
    $b = ($colour & 0x0000FF);

    $alphaStep = ($endAlpha - $startAlpha) / ($bottom - $top);
    $alpha = $startAlpha;

    for($y = $top; $y < $bottom; $y++){
        $col = imagecolorallocatealpha($image, $r, $g, $b, round($alpha));
        imagefilledrectangle($image, $left, $y, $right, $y + 1, $col);

        $alpha += $alphaStep;
    }
}

function writeCenteredTtfText($image, $font, $text, $colour, $x, $y, $width, $height){
    $lines = explode("\n", $text);
    $fontSize = 100;
    $res = imagettfbbox($fontSize, 0, $font, $text);

    if($res[4] > $width){
        $scale = $width / $res[4];
        $fontSize = round(($fontSize * $scale)) - 10;
    }

    $yOffset = 0;

    foreach($lines as $line){
        $res = imagettfbbox($fontSize, 0, $font, $line);
        $textWidth = $res[2] - $res[0];
        $textHeight = $res[7] - $res[1];

        $centerX = ($width / 2) - ($textWidth / 2);
        $centerY = ($height / 2) - ($textHeight / 2);

        imagettftext($image, $fontSize, 0, round($x + $centerX), round($y + $centerY - $yOffset), $colour, $font, $line);
        $yOffset += $textHeight * 1.5;
    }
}