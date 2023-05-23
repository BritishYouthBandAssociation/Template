<?php

function loadImage($filePath){
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

function loadRemoteImage($url){
    $filePath = tempnam(sys_get_temp_dir(), "IMG_");
    $data = file_get_contents($url);
    file_put_contents($filePath, $data);

    return loadImage($filePath);
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
    $fontSize = $height / (count($lines) * 1.5);
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

function fitImageToCanvas($image, $canvas){
	$w = imagesx($image);
	$h = imagesy($image);
	$canvasW = imagesx($canvas);
	$canvasH = imagesy($canvas);

	if($canvasH >= $canvasW){
		$multiplier = $h / $canvasH;
		$resizeDir = 1;
	} else {
		$multiplier = $w / $canvasW;
		$resizeDir = 2;
	}

	$targetW = $w / $multiplier;
	$targetH = $h / $multiplier;

	$srcX = ($resizeDir == 1) ? $w / 4 : 0;
	$srcY = ($resizeDir == 2) ? $h / 4 : 0;

	imagecopyresized($canvas, $image, 0, 0, round($srcX), round($srcY), round($targetW), round($targetH), $w, $h);
}

function toBool($val){
	if(strtolower($val) == "false"){
		return false;
	}

	return !!$val;
}