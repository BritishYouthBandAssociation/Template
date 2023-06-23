<?php

require_once("BaseTemplate.php");
require_once(__DIR__ . "/../ResourceTypes.php");

class SquareNewsTemplate extends BaseTemplate {
	const WIDTH = 1000;
	const HEIGHT = 1000;

	protected $params = array(
		"image" => ResourceTypes::IMAGE,
		"text" => ResourceTypes::STRING
	);

	public function render(){
		fitImageToCanvas($this->params["image"], $this->canvas);
		alphaGradient($this->canvas, 0, self::HEIGHT * 0.2, self::WIDTH, self::HEIGHT, 127, 10, $this->colours["primary"]);
		writeCenteredTtfText($this->canvas, $this->fonts["title"], strtoupper($this->params["text"]), $this->colours["onPrimary"], self::WIDTH / 8, self::HEIGHT * 0.75, self::WIDTH * 0.75, self::HEIGHT * 0.2);
	}

	protected function getFileName(){
		return $this->params["text"];
	}
}