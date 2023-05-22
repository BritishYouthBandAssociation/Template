<?php

class InstagramNewsTemplate{
	const TARGET_SIZE = 1000;

	public function render($image, $text){
		$canvas = imagecreatetruecolor(self::TARGET_SIZE, self::TARGET_SIZE);
		
		fitImageToCanvas($image, $canvas);
		alphaGradient($canvas, 0, self::TARGET_SIZE * 0.2, self::TARGET_SIZE, self::TARGET_SIZE, 127, 10, 0x282360);
		writeCenteredTtfText($canvas, "../asket.ttf", $text, imagecolorallocate($image,255,255,255), self::TARGET_SIZE / 6, self::TARGET_SIZE * 0.75, self::TARGET_SIZE * 0.666, self::TARGET_SIZE * 0.1);

		return $canvas;
	}
}