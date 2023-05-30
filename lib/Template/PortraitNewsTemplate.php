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
		"version" => [ResourceTypes::STRING, ResourceTypes::OPTIONAL],
		"subtitle" => [ResourceTypes::STRING, ResourceTypes::OPTIONAL]
	);

	protected function fillDefaultParams(){
		$this->params["version"] ??= 2;
		$this->params["subtitle"] ??= "";
	}

	protected function validateParams(){
		if ($this->params["version"] > 2 || $this->params["version"] < 1) {
			return false;
		}

		return true;
	}

	public function render(){
		if($this->params["version"] == 1){
			imagefill($this->canvas, 0, 0, $this->colours["primary"]);
			fitImageToSpace($this->params["image"], $this->canvas, 0, 0, self::WIDTH, self::HEIGHT * 0.4);
			imagefilledrectangle($this->canvas, 0, self::HEIGHT * 0.4, self::WIDTH, self::HEIGHT * 0.41, $this->colours["highlight"]);

			writeCenteredTtfText($this->canvas, $this->fonts["title"], strtoupper($this->params["title"]), $this->colours["onPrimary"], self::WIDTH * self::PADDING_X, self::HEIGHT * 0.45, self::WIDTH * (1 - (self::PADDING_X * 2)), self::HEIGHT * 0.25);
			writeCenteredTtfText($this->canvas, $this->fonts["title"], "BYBA.ONLINE", $this->colours["onPrimary"], self::WIDTH * self::PADDING_X, self::HEIGHT * 0.75, self::WIDTH * (1 - (self::PADDING_X * 2)), 24);

			$logo = $this->getImage("byba.png");
			containImageInSpace($logo, $this->canvas, self::WIDTH * 0.4, self::HEIGHT * 0.85, self::WIDTH * 0.2, self::HEIGHT * 0.1);
		} else {
			$stripW = self::WIDTH * 0.1;
			$stripCol = makeColourTransparent($this->canvas, $this->colours["primary"], 90);

			imagefill($this->canvas, 0,self::HEIGHT * 0.2, $this->colours["light"]);

			writeCenteredTtfText($this->canvas, $this->fonts["title"], strtoupper($this->params["title"]), $this->colours["dark"], $stripW + (self::WIDTH * self::PADDING_X), self::HEIGHT * (self::PADDING_Y + 0.02), (self::WIDTH * (1 - (self::PADDING_X * 2))) - $stripW, self::HEIGHT * (0.1 - (self::PADDING_Y * 2)));
			writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], $this->params["subtitle"], $this->colours["dark"], $stripW + (self::WIDTH * self::PADDING_X), self::HEIGHT * (self::PADDING_Y + 0.12), (self::WIDTH * (1 - (self::PADDING_X * 2))) - $stripW, self::HEIGHT * (0.04 - (self::PADDING_Y * 2)));

			fitImageToSpace($this->params["image"], $this->canvas, 0, self::HEIGHT * 0.2, self::WIDTH, self::HEIGHT * 0.8);

			imagefilledrectangle($this->canvas, 0, 0, $stripW, self::HEIGHT * 0.2, $this->colours["primary"]);
			imagefilledrectangle($this->canvas, 0, self::HEIGHT * 0.2, $stripW, self::HEIGHT, $stripCol);
		}
	}
}