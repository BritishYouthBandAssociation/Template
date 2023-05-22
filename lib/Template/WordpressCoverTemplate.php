<?php

require_once("BaseTemplate.php");
require_once(__DIR__ . "/../ResourceTypes.php");

class WordpressCoverTemplate extends BaseTemplate {
	const WIDTH = 800;
	const HEIGHT = 350;

	protected $params = array(
		"image" => ResourceTypes::IMAGE
	);

	public function render(){
		$canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
		fitImageToCanvas($this->params["image"], $canvas);
		return $canvas;
	}
}