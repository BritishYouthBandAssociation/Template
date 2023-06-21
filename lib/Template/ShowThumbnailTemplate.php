<?php

require_once("BaseTemplate.php");
require_once(__DIR__ . "/../ResourceTypes.php");

class ShowThumbnailTemplate extends BaseTemplate {
	const WIDTH = 1080;
	const HEIGHT = 720;

	protected $params = array(
		"image" => ResourceTypes::IMAGE,
		"title" => ResourceTypes::STRING,
		"show" => ResourceTypes::STRING,
		"date" => ResourceTypes::STRING
	);

	public function render(){
		fitImageToCanvas($this->params["image"], $this->canvas);

		$overlayCol = makeColourTransparent($this->canvas, $this->colours["primary"], 80);
		imagefilledrectangle($this->canvas, 0, 0, self::WIDTH, self::HEIGHT, $overlayCol);

		$logo = $this->getImage("light logo.png");
		containImageInSpace	($logo, $this->canvas, self::WIDTH * 0.4, self::HEIGHT * 0.1, self::WIDTH * 0.2, self::HEIGHT * 0.2);

		writeCenteredTtfText($this->canvas, $this->fonts["title"], strtoupper($this->params["title"]), $this->colours["onPrimary"], self::WIDTH * 0.1, self::HEIGHT * 0.35, self::WIDTH * 0.8, self::HEIGHT * 0.3, fitToOneLine: true, outlineColour: $this->colours["primary"]);

		writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], $this->params["show"], $this->colours["onPrimary"], self::WIDTH * 0.1, self::HEIGHT * 0.7, self::WIDTH * 0.4, self::HEIGHT * 0.03, fitToOneLine: true);

		writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], $this->params["date"], $this->colours["onPrimary"], self::WIDTH * 0.5, self::HEIGHT * 0.7, self::WIDTH * 0.4, self::HEIGHT * 0.03, fitToOneLine: true);
	}
}