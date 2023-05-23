<?php

require_once("BaseTemplate.php");
require_once(__DIR__ . "/../ResourceTypes.php");

class SquareNewsTemplate extends BaseTemplate {
	const TARGET_SIZE = 1000;

	protected $params = array(
		"image" => ResourceTypes::IMAGE,
		"text" => ResourceTypes::WRAPPED_STRING
	);

	public function render(){
		$canvas = imagecreatetruecolor(self::TARGET_SIZE, self::TARGET_SIZE);

		fitImageToCanvas($this->params["image"], $canvas);
		alphaGradient($canvas, 0, self::TARGET_SIZE * 0.2, self::TARGET_SIZE, self::TARGET_SIZE, 127, 10, $this->config->brandingBlue);
		writeCenteredTtfText($canvas, "../asket.ttf", strtoupper($this->params["text"]), imagecolorallocate($this->params["image"],255,255,255), self::TARGET_SIZE / 6, self::TARGET_SIZE * 0.7, self::TARGET_SIZE * 0.666, self::TARGET_SIZE * 0.2);

		return $canvas;
	}
}