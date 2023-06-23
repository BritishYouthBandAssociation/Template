<?php

require_once("BaseTemplate.php");
require_once(__DIR__ . "/../ResourceTypes.php");

class PhotoPassTemplate extends BaseTemplate {
	const WIDTH = 1748;
	const HEIGHT = 2480;

	const PADDING = 200;

	protected $params = array(
		"photographer" => ResourceTypes::STRING,
		"showName" => ResourceTypes::STRING,
		"date" => ResourceTypes::STRING
	);

	public function render(){
		imageresolution($this->canvas, 300);
		imagefill($this->canvas, 0, 0, $this->colours["primary"]);
		imagefilledrectangle($this->canvas, 0, self::HEIGHT * 0.8, self::WIDTH, self::HEIGHT, $this->colours["highlight"]);

		$logo = $this->getImage("dark logo.png", 0.6);
		containImageInSpace($logo, $this->canvas, self::WIDTH * 0.2, self::HEIGHT * 0.3, self::WIDTH * 0.6, self::HEIGHT * 0.6);

		$textW = self::WIDTH - (self::PADDING * 2);

		writeCenteredTtfText($this->canvas, $this->fonts["title"], "BRITISH YOUTH BAND ASSOCIATION", $this->colours["onPrimary"], self::PADDING, self::HEIGHT * 0.1, $textW, 300);
		
		writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], $this->params["photographer"], $this->colours["onPrimary"], self::PADDING, self::HEIGHT * 0.4, $textW, 150);
		writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], $this->params["showName"] . ", " . $this->params["date"], $this->colours["onPrimary"], self::PADDING, self::HEIGHT * 0.6, $textW, 150);
		
		writeCenteredTtfText($this->canvas, $this->fonts["title"], "PHOTOGRAPHY PASS", $this->colours["onPrimary"], self::PADDING, self::HEIGHT * 0.85, $textW, self::HEIGHT * 0.1);
	}

	protected function getFileName(){
		return $this->params["photographer"] . " Photo Pass for " . $this->params["showName"];
	}
}