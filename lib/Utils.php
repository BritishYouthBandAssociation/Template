<?php

require_once("Alignment.php");

function loadImage($filePath) {
	$type = exif_imagetype($filePath);
	switch ($type) {
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
			throw new Exception("Unrecognised mime type $type for file $filePath");
	}
}

function makeRequest($url) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	if (defined('CURL_PORT')) {
		curl_setopt($curl, CURLOPT_LOCALPORT, CURL_PORT);
	}

	if (defined('CURL_RANGE')) {
		curl_setopt($curl, CURLOPT_LOCALPORTRANGE, CURL_RANGE);
	}

	$res = curl_exec($curl);

	if ($res === FALSE) {
		$res = null;
	}

	curl_close($curl);

	return $res;
}

function loadRemoteImage($url) {
	$data = makeRequest($url);
	if ($data == null) {
		return null;
	}

	$filePath = tempnam(sys_get_temp_dir(), "IMG_");
	file_put_contents($filePath, $data);

	return loadImage($filePath);
}

function hex2rgb($colour) {
	$r = ($colour & 0xFF0000) >> 16;
	$g = ($colour & 0x00FF00) >> 8;
	$b = ($colour & 0x0000FF);

	return [$r, $g, $b];
}

function hex2imageColour($colour, $image) {
	[$r, $g, $b] = hex2rgb($colour);
	return imagecolorallocate($image, $r, $g, $b);
}

function alphaGradient($image, $left, $top, $right, $bottom, $startAlpha, $endAlpha, $colour = 0) {
	[$r, $g, $b] = hex2rgb($colour);

	$alphaStep = ($endAlpha - $startAlpha) / ($bottom - $top);
	$alpha = $startAlpha;

	for ($y = $top; $y < $bottom; $y++) {
		$col = imagecolorallocatealpha($image, $r, $g, $b, round($alpha));
		imagefilledrectangle($image, $left, $y, $right, $y + 1, $col);

		$alpha += $alphaStep;
	}
}

function wrap($fontSize, $angle, $fontFace, $string, $width) {
	$ret = "";
	$arr = explode(' ', $string);

	foreach ($arr as $word) {

		$teststring = $ret . ' ' . $word;
		$testbox = imagettfbbox($fontSize, $angle, $fontFace, $teststring);
		if ($testbox[2] > $width) {
			$ret .= ($ret == "" ? "" : "\n") . $word;
		} else {
			$ret .= ($ret == "" ? "" : ' ') . $word;
		}
	}

	return $ret;
}

function calculateFontSize($text, $font, $width, $height, $max = 100, $fitToOneLine = false) {
	$size = $max;

	while (true) {
		$bounds = imagettfbbox($size, 0, $font, $text);
		$calcWidth = $bounds[2] - $bounds[0];
		$calcHeight = $bounds[1] - $bounds[7];

		if ($calcWidth <= $width && $calcHeight <= $height) {
			return $size;
		}

		if (!$fitToOneLine) {
			$rows = ($height / $calcHeight) / 1.6;

			if ($calcWidth / $rows <= $width) {
				$actualRows = 1;

				if (!$fitToOneLine) {
					$actualRows = count(explode("\n", wrap($size, 0, $font, $text, $width)));
				}

				if ($actualRows <= $rows) {
					return $size;
				}
			}
		}

		$size *= 0.98;
	}
}

function writeCenteredTtfText($image, $font, $text, $colour, $x, $y, $width, $height, $maxSize = 999, $fitToOneLine = false, $outlineColour = null) {
	$fontSize = calculateFontSize($text, $font, $width, $height, $maxSize, $fitToOneLine);

	if ($fitToOneLine) {
		$lines = array($text);
	} else {
		$lines = explode("\n", wrap($fontSize, 0, $font, $text, $width));
	}

	$yOffset = $fontSize;

	$expectedRows = $height / ($fontSize * 1.5);
	if ($expectedRows > count($lines)) {
		$missingRows = $expectedRows - count($lines);
		$yOffset += $missingRows * $fontSize;
	}

	foreach ($lines as $line) {
		$res = imagettfbbox($fontSize, 0, $font, $line);
		$textWidth = $res[2] - $res[0];
		$textHeight = $res[1] - $res[7];

		$centerX = ($width / 2) - ($textWidth / 2);
		$centerY = 0;

		imagettftext($image, $fontSize, 0, round($x + $centerX), round($y + $centerY + $yOffset), $colour, $font, $line);

		if($outlineColour != null){
			strokedOutline($image, $fontSize, round($x + $centerX), round($y + $centerY + $yOffset), $outlineColour, $font, $line, $fontSize / 25);
		}

		$yOffset += $textHeight * 1.5;
	}
}

function calculateAlignment($alignment, $size, $canvasSize) {
	if ($alignment == Alignment::START) {
		return 0;
	}

	if ($alignment == Alignment::CENTER) {
		return ($size - $canvasSize) / 2;
	}

	//end
	return max(0, $size - $canvasSize);
}

function fitImageToCanvas($image, $canvas, $alignment = Alignment::CENTER) {
	$canvasW = imagesx($canvas);
	$canvasH = imagesy($canvas);

	fitImageToSpace($image, $canvas, 0, 0, $canvasW, $canvasH, $alignment);
}

function fitImageToSpace($image, $canvas, $canvasX, $canvasY, $canvasW, $canvasH, $alignment = Alignment::CENTER) {
	$w = imagesx($image);
	$h = imagesy($image);
	$aspect = $w / $h;

	$canvasAspect = $canvasW / $canvasH;

	if ($aspect >= $canvasAspect) {
		$targetH = $h;
		$targetW = ceil(($targetH * $canvasW) / $canvasH);
		$srcX = calculateAlignment($alignment, $w, $targetW);
		$srcY = 0;
	} else {
		$targetW = $w;
		$targetH = ceil(($targetW * $canvasH) / $canvasW);
		$srcX = 0;
		$srcY = calculateAlignment($alignment, $h, $targetH);
	}

	imagecopyresampled($canvas, $image, $canvasX, $canvasY, $srcX, $srcY, $canvasW, $canvasH, $targetW, $targetH);
}

function containImageInSpace($image, $canvas, $canvasX, $canvasY, $canvasW, $canvasH) {
	$w = imagesx($image);
	$h = imagesy($image);
	$aspect = $w / $h;

	$canvasAspect = $canvasW / $canvasH;

	if ($aspect >= $canvasAspect) {
		$canvasH = $canvasW * ($h / $w);
	} else {
		$canvasW = $canvasH * ($w / $h);
	}

	imagecopyresampled($canvas, $image, $canvasX, $canvasY, 0, 0, $canvasW, $canvasH, $w, $h);
}

function dbgRect($img, $top, $left, $right, $bottom) {
	imagefilledrectangle($img, $top, $left, $right, $bottom, imagecolorallocatealpha($img, 255, 0, 0, 0.4));
}

function toBool($val) {
	if (strtolower($val) == "false") {
		return false;
	}

	return !!$val;
}

function resolveType($types) {
	if (is_array($types)) {
		$val = 0;
		foreach ($types as $type) {
			$val = $val | $type->value;
		}

		return $val;
	}

	return $types->value;
}

function makeColourTransparent($image, $colour, $transparency) {
	$rgb = imagecolorsforindex($image, $colour);
	return imagecolorallocatealpha($image,  $rgb["red"], $rgb["green"], $rgb["blue"], 127 - $transparency);
}

function strokedOutline($image, $size, $x, $y, $strokeColour, $font, $text, $stroke) {
	$res = imagettfbbox($size + $stroke, 0, $font, $text);
	$textWidth = $res[2] - $res[0];
	$textHeight = $res[1] - $res[7] + 2;

	$img = imagecreatetruecolor($textWidth + $stroke, $textHeight + $stroke);
	$black = imagecolorallocate($img, 0, 0, 0);

	imagettfstroketext($img, $size, 0, 0, $textHeight - ($stroke / 2), $black, $strokeColour, $font, $text, $stroke);

	imagecolortransparent($img, $black);
	imagecopy($image, $img, $x, $y - $textHeight + ($stroke / 2), 0, 0, $textWidth + $stroke, $textHeight + $stroke);

	/*header("Content-Type: image/png");
	imagepng($img);
	die();*/

	imagedestroy($img);

	return [$textWidth - $stroke, $textHeight];
}

function alternatingTextStroke($image, $size, $x, $y, $width, $font, $text, $colour, $strokeColour, $stroke) {
	$outline = true;

	while (true) {
		if ($outline) {
			list($w, $h) = strokedOutline($image, $size, $x, $y, $strokeColour, $font, $text, $stroke);
		} else {
			$res = imagettfbbox($size, 0, $font, $text);
			$w = $res[2] - $res[0];

			imagettftext($image, $size, 0, $x - 10, $y + $size + ($stroke / 2), $colour, $font, $text);
		}

		$x += $w;
		$outline = !$outline;

		if ($x >= $width) {
			break;
		}
	}
}

function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {
	for ($c1 = ($x - abs($px)); $c1 <= ($x + abs($px)); $c1++) {
		for ($c2 = ($y - abs($px)); $c2 <= ($y + abs($px)); $c2++) {
			imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
		}
	}

	return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
}
