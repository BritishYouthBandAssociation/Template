<?php

require_once("BaseTemplate.php");
require_once(__DIR__ . "/../ResourceTypes.php");
require_once(__DIR__ . "/../Alignment.php");

class CaptionAwardTemplate extends BaseTemplate {
	const WIDTH = 1080;
	const HEIGHT = 1080;

	protected $params = array(
		"image" => ResourceTypes::IMAGE,
		"caption" => ResourceTypes::STRING,
		"band" => ResourceTypes::STRING
	);

	public function render(){
		fitImageToCanvas($this->params["image"], $this->canvas);

		$overlayCol = makeColourTransparent($this->canvas, hex2imageColour(0x0e0199, $this->canvas), 50);
		imagefilledrectangle($this->canvas, 0, 0, self::WIDTH, self::HEIGHT, $overlayCol);

		fitText($this->canvas, $this->fonts["title"], strtoupper($this->params["caption"]), $this->colours["onPrimary"], self::WIDTH / 4, self::HEIGHT * 0.35, self::WIDTH / 2, self::HEIGHT * 0.2, Alignment::CENTER);

		fitText($this->canvas, $this->fonts["subtitle"], $this->params["band"], $this->colours["primary"], self::WIDTH * 0.31, self::HEIGHT * 0.55, self::WIDTH * .38, self::HEIGHT * .07, Alignment::CENTER, fitToOneLine: true, bgColour: $this->colours["onPrimary"]);
	}
}