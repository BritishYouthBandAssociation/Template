<?php

require_once(__DIR__ . "/../ResourceTypes.php");
require_once(__DIR__ . "/../Utils.php");

abstract class BaseTemplate {
	abstract public function render();

	protected $config;
	protected $params;

	public $canvas = null;
	protected $colours;
	protected $fonts;

	const WIDTH = 0;
	const HEIGHT = 0;

	public function __construct($config){
		$this->config = $config;

		//set up some defaults
		$this->canvas = imagecreatetruecolor(static::WIDTH, static::HEIGHT);
		$this->colours = array(
			"primary" => hex2imageColour($this->config->colourPrimary, $this->canvas),
			"onPrimary" => hex2imageColour($this->config->colourOnPrimary, $this->canvas),
			"highlight" => hex2imageColour($this->config->colourHighlight, $this->canvas),
			"light" => hex2imageColour($this->config->colourLight, $this->canvas),
			"dark" => hex2imageColour($this->config->colourDark, $this->canvas)
		);
		$this->fonts = array(
			"title" => $this->getFont("asket.ttf"),
			"subtitle" => $this->getFont("open-sans.ttf")
		);
	}

	public function parseParams(){		
		foreach($this->params as $param => $types){
			$type = resolveType($types);

			$val = null;
			if($type & ResourceTypes::IMAGE->value){
				$val = $this->getImageParam($param);
			} else if($type & ResourceTypes::STRING->value){
				$val = $this->getTextParam($param);
			}

			if($val == null && !($type & ResourceTypes::OPTIONAL->value)){
				throw new Exception("Failed to process parameter '$param'");
			}

			$this->params[$param] = $val;
		}

		$this->fillDefaultParams();

		if(!$this->validateParams()){
			throw new Exception("Failed to validate parameters");
		}
	}

	protected function fillDefaultParams(){

	}

	protected function validateParams(){
		return true;
	}

	protected function getFont($font){
		return $this->config->fontDir . $font;
	}

	protected function getImage($image){
		return loadImage($this->config->imageDir . $image);
	}

	private function getImageParam($param){
		if(isset($_FILES[$param])){
			return loadImage($_FILES[$param]['tmp_name']);
		} else if(isset($_REQUEST[$param])) {
			return loadRemoteImage($_REQUEST[$param]);
		}

		return null;
	}

	private function getTextParam($param){
		if(!isset($_REQUEST[$param])){
			return null;
		}

		return trim($_REQUEST[$param]);
	}
}