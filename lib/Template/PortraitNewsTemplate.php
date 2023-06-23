<?php

require_once("BaseTemplate.php");
require_once(__DIR__ . "/../ResourceTypes.php");

class PortraitNewsTemplate extends BaseTemplate {
	const WIDTH = 1080;
	const HEIGHT = 1920;

	const PADDING_X = 0.05;
	const PADDING_Y = 0.01;

	protected $params = array(
		"image" => ResourceTypes::IMAGE,
		"title" => ResourceTypes::STRING,
		"subtitle" => [ResourceTypes::STRING, ResourceTypes::OPTIONAL]
	);

	protected function fillDefaultParams() {
		$this->params["subtitle"] ??= "";
	}

	public function render() {
		imagefill($this->canvas, 0, 0, $this->colours["primary"]);
		fitImageToSpace($this->params["image"], $this->canvas, 0, 0, self::WIDTH, self::HEIGHT * 0.4);
		alphaGradient($this->canvas, 0, self::HEIGHT * 0.2, self::WIDTH, self::HEIGHT * 0.4, 127, 0, $this->colours["primary"]);

		writeCenteredTtfText($this->canvas, $this->fonts["title"], strtoupper($this->params["title"]), $this->colours["onPrimary"], self::WIDTH * self::PADDING_X, self::HEIGHT * 0.45, self::WIDTH * (1 - (self::PADDING_X * 2)), self::HEIGHT * 0.25);
		writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], $this->params["subtitle"], $this->colours["onPrimary"], self::WIDTH * self::PADDING_X, self::HEIGHT * 0.75, self::WIDTH * (1 - (self::PADDING_X * 2)), self::HEIGHT * 0.025);

		$logo = $this->getImage("byba.png");
		containImageInSpace($logo, $this->canvas, self::WIDTH * 0.4, self::HEIGHT * 0.85, self::WIDTH * 0.2, self::HEIGHT * 0.1);
	}

	protected function getFileName(){
		return "Portrait News for " . $this->params["title"];
	}
}
