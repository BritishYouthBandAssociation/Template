<?php

require_once("Alignment.php");

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
		case 18:
			return imageCreateFromWebp($filePath);
        default:
            return null;
    }
}

function makeRequest($url){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	if(defined('CURL_PORT')){
		curl_setopt($curl, CURLOPT_LOCALPORT, CURL_PORT);
	}

	if(defined('CURL_RANGE')){
		curl_setopt($curl, CURLOPT_LOCALPORTRANGE, CURL_RANGE);
	}

	$res = curl_exec($curl);

	if($res === FALSE){
		$res = null;
	}

	curl_close($curl);

	return $res;
}

function loadRemoteImage($url){
	$data = makeRequest($url);
	if($data == null){
		return null;
	}

    $filePath = tempnam(sys_get_temp_dir(), "IMG_");
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

function wrap($fontSize, $angle, $fontFace, $string, $width){
    $ret = "";
    $arr = explode(' ', $string);
    
    foreach ( $arr as $word ){
    
        $teststring = $ret.' '.$word;
        $testbox = imagettfbbox($fontSize, $angle, $fontFace, $teststring);
        if ( $testbox[2] > $width ){
            $ret.=($ret==""?"":"\n").$word;
        } else {
            $ret.=($ret==""?"":' ').$word;
        }
    }
    
    return $ret;
}

function calculateFontSize($text, $font, $width, $height){
	$charsToFit = strlen($text);
	$size = 100;

	while(true){
		$bounds = imagettfbbox($size, 0, $font, $text);
		$calcWidth = $bounds[2] - $bounds[0];
		$calcHeight = $bounds[1] - $bounds[7];

		if($bounds[2] <= $width){
			return $size;
		}

		$rows = ($height / $calcHeight) / 1.6;
		if($calcWidth / $rows <= $width){
			$actualRows = count(explode("\n", wrap($size, 0, $font, $text, $width)));

			if($actualRows <= $rows){
				return $size;
			}
		}

		$size *= 0.98;
	}
}

function writeCenteredTtfText($image, $font, $text, $colour, $x, $y, $width, $height, $maxSize = 100){
	$fontSize = calculateFontSize($text, $font, $width, $height);
	$lines = explode("\n", wrap($fontSize, 0, $font, $text, $width));
    $yOffset = $fontSize;

	$expectedRows = $height / ($fontSize * 1.5);
	if($expectedRows > count($lines)){
		$missingRows = $expectedRows - count($lines);
		$yOffset += $missingRows * $fontSize;
	}

    foreach($lines as $line){
        $res = imagettfbbox($fontSize, 0, $font, $line);
        $textWidth = $res[2] - $res[0];
        $textHeight = $res[1] - $res[7];

        $centerX = ($width / 2) - ($textWidth / 2);
        $centerY = 0;

        imagettftext($image, $fontSize, 0, round($x + $centerX), round($y + $centerY + $yOffset), $colour, $font, $line);
        $yOffset += $textHeight * 1.5;
    }
}

function calculateAlignment($alignment, $size, $canvasSize){
	if($size <= $canvasSize){
		return 0;
	}

	if($alignment == Alignment::START){
		return 0;
	}

	if($alignment == Alignment::CENTER){
		return $size / 4;
	}

	//end
	return max(0, $size - $canvasSize);
}

function fitImageToCanvas($image, $canvas, $alignment = Alignment::CENTER){
	$w = imagesx($image);
	$h = imagesy($image);
	$canvasW = imagesx($canvas);
	$canvasH = imagesy($canvas);

	if($w >= $h){
		$multiplier = $h / $canvasH;
		$resizeDir = 1;
	} else {
		$multiplier = $w / $canvasW;
		$resizeDir = 2;
	}

	$targetW = $w / $multiplier;
	$targetH = $h / $multiplier;

	$srcX = ($resizeDir == 1) ? calculateAlignment($alignment, $w, $canvasW * $multiplier) : 0;
	$srcY = ($resizeDir == 2) ? calculateAlignment($alignment, $h, $canvasH * $multiplier) : 0;

	imagecopyresized($canvas, $image, 0, 0, round($srcX), round($srcY), round($targetW), round($targetH), $w, $h);
}

function toBool($val){
	if(strtolower($val) == "false"){
		return false;
	}

	return !!$val;
}