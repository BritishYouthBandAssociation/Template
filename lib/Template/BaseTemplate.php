<?php

require_once(__DIR__ . "/../ResourceTypes.php");
require_once(__DIR__ . "/../Utils.php");

abstract class BaseTemplate {
	abstract public function render();

	protected $config;
	protected $params = array();

	public $canvas = null;
	protected $colours;
	protected $fonts;

	public $fileName;

	const WIDTH = 0;
	const HEIGHT = 0;

	public function __construct($config) {
		$this->config = $config;
		$this->fileName = str_replace("Template", "", get_class($this));

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

	public function parseParams() {
		foreach ($this->params as $param => $types) {
			$type = resolveType($types);
			$isArray = $type & ResourceTypes::ARRAY->value;

			$val = null;
			if ($type & ResourceTypes::IMAGE->value) {
				$val = $this->getImageParam($param, $isArray);
			} else if ($type & ResourceTypes::STRING->value) {
				$val = $this->getTextParam($param, $isArray);
			}

			if ($val == null && !($type & ResourceTypes::OPTIONAL->value)) {
				throw new Exception("Failed to process parameter '$param'");
			}

			$this->params[$param] = $val;
		}

		$this->fillDefaultParams();

		if (!$this->validateParams()) {
			throw new Exception("Failed to validate parameters");
		}
	}

	protected function fillDefaultParams() {
	}

	protected function validateParams() {
		return true;
	}

	protected function getFont($font) {
		return $this->config->fontDir . $font;
	}

	protected function getImage($image, $opacity = null) {
		$img = loadImage($this->config->imageDir . $image);
		if ($opacity == null || $opacity == 1) {
			return $img;
		}

		$transparency = 1 - $opacity;
		imagefilter($img, IMG_FILTER_COLORIZE, 0, 0, 0, 127 * $transparency);

		return $img;
	}

	private function getImageParam($param, $isArray = false) {
		if (isset($_FILES[$param])) {
			return loadImage($_FILES[$param]['tmp_name']);
		} else if (isset($_REQUEST[$param])) {
			return loadRemoteImage($_REQUEST[$param]);
		}

		return null;
	}

	private function getTextParam($param, $isArray = false) {
		if (!isset($_REQUEST[$param])) {
			return null;
		}

		if (!$isArray) {
			return trim($_REQUEST[$param]);
		}

		$data = [];
		foreach ($_REQUEST[$param] as $val) {
			$data[] = trim($val);
		}

		return $data;
	}
}
