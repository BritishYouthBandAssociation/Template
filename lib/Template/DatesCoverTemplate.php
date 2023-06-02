<?php

require_once("BaseTemplate.php");
require_once(__DIR__ . "/../ResourceTypes.php");

class DatesCoverTemplate extends BaseTemplate {
	const WIDTH = 2160;
	const HEIGHT = 1216;

	protected $params = array(
		"bannerText" => ResourceTypes::STRING,
		"nextShow" => ResourceTypes::STRING,
		"nextLocation" => ResourceTypes::STRING,
		"nextDate" => ResourceTypes::STRING,
		"shows" => [ResourceTypes::STRING, ResourceTypes::ARRAY],
		"locations" => [ResourceTypes::STRING, ResourceTypes::ARRAY]
	);

	protected function validateParams()
	{
		return count($this->params["shows"]) == count($this->params["locations"]);
	}

	public function render(){
		imagefill($this->canvas, 0, 0, $this->colours["primary"]);
		alternatingTextStroke($this->canvas, 24, -20, 10, self::WIDTH, $this->fonts["title"], $this->params["bannerText"], $this->colours["light"], $this->colours["light"], 2);
		alternatingTextStroke($this->canvas, 24, -350, self::HEIGHT - 34, self::WIDTH, $this->fonts["title"], $this->params["bannerText"], $this->colours["light"], $this->colours["light"], 2);

		$this->drawNextShow();
		$this->drawDates();
	}

	private function drawNextShow(){
		$top = self::HEIGHT / 3;
		$left = self::WIDTH / 8;
		$width = self::WIDTH / 3;

		$padding = 20;
		$headerSize = 45;
		$dateSize = 250;

		writeCenteredTtfText($this->canvas, $this->fonts["title"], "NEXT SHOW", $this->colours["onPrimary"], $left, $top, $width, $headerSize);
		$top += $padding + $headerSize;

		writeCenteredTtfText($this->canvas, $this->fonts["title"], $this->params["nextDate"], $this->colours["onPrimary"], $left, $top, $width, $dateSize);
		$top += $padding + $dateSize;

		writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], $this->params["nextShow"], $this->colours["onPrimary"], $left, $top, $width, $headerSize);
		$top += $padding + $headerSize;

		writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], $this->params["nextLocation"], $this->colours["onPrimary"], $left, $top, $width, $headerSize);
	}

	private function drawDates(){
		$left = self::WIDTH * 0.5;
		$width = self::WIDTH / 3;
		
		$padding = 50;
		$size = 30;
		$subtitle = $size * 0.8;
		$subtitlePadding = $padding / 2;

		$estimatedSize = count($this->params["shows"]) * ($size + $padding + $subtitle + $subtitlePadding);
		$top = (self::HEIGHT / 2) - ($estimatedSize / 2);

		for($i = 0; $i < count($this->params["shows"]); $i++){
			writeCenteredTtfText($this->canvas, $this->fonts["title"], strtoupper($this->params["shows"][$i]), $this->colours["onPrimary"], $left, $top, $width, $size, $size);
			$top += $size + $subtitlePadding;
			writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], $this->params["locations"][$i], $this->colours["onPrimary"], $left, $top, $width, $subtitle);
			$top += $size + $padding;
		}
	}
}