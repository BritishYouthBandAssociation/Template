<?php

require_once(__DIR__ . "/../ResourceTypes.php");

class InstagramNewsTemplate{
	const TARGET_SIZE = 1000;

	private $params = array(
		"image" => ResourceTypes::IMAGE,
		"text" => ResourceTypes::WRAPPED_STRING
	);

	private $config;

	public function __construct($config){
		$this->config = $config;
	}

	public function parseParams(){		
		foreach($this->params as $param => $type){
			if($type == ResourceTypes::IMAGE){
				$image = null;
				if(isset($_FILES[$param])){
					$image = loadImage($_FILES[$param]['tmp_name']);
				} else if(isset($_REQUEST[$param])) {
					$image = loadRemoteImage($_REQUEST[$param]);
				}

				if($image == null){
					throw new Exception("Failed to process parameter '$param'");
				}

				$this->params[$param] = $image;
			} else if($type == ResourceTypes::WRAPPED_STRING){
				if(!isset($_REQUEST[$param])){
					throw new Exception("Failed to process parameter '$param'");
				}

				$this->params[$param] = wordwrap($_REQUEST[$param], $this->config->wordwrapLength, "\n");
			} else {
				$this->params[$param] = $_GET[$param];
			}
		}
	}

	public function render(){
		$canvas = imagecreatetruecolor(self::TARGET_SIZE, self::TARGET_SIZE);

		fitImageToCanvas($this->params["image"], $canvas);
		alphaGradient($canvas, 0, self::TARGET_SIZE * 0.2, self::TARGET_SIZE, self::TARGET_SIZE, 127, 10, 0x282360);
		writeCenteredTtfText($canvas, "../asket.ttf", strtoupper($this->params["text"]), imagecolorallocate($this->params["image"],255,255,255), self::TARGET_SIZE / 6, self::TARGET_SIZE * 0.75, self::TARGET_SIZE * 0.666, self::TARGET_SIZE * 0.1);

		return $canvas;
	}
}